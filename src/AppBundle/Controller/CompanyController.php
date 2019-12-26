<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Common\ThreeSecrets;
use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\Config;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Landing\LandingPage;
use AppBundle\Entity\Order;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use AppBundle\Entity\Subscriber;
use AppBundle\Entity\User;
use AppBundle\Form\Type\OrderType;
use AppBundle\Form\Type\SubscribeType;
use AppBundle\Helper\ConstantHelper;
use AppBundle\Service\Pdf\PdfCreator;
use AppBundle\Validator\Constraints\EmailNotUsed;
use AppBundle\Validator\Constraints\ThreeSecretsNotUsed;
use function Couchbase\defaultDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompanyController extends BaseController
{

    public function orderAction(Product $product, Request $request, Session $session)
    {
        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);
        $errorMessage = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $order = new Order();
            $order->makeCode()->setEmail($data['email'])
                ->setTotal($product->getPrice())->setStatus(Order::STATUS_NEW)
                ->setProduct($product);
            $this->em->persist($order);
            $this->em->flush();

            $response = $this->get('app.payment')->payByOrder($order, $this->generateUrl('payment_finish', [], UrlGeneratorInterface::ABSOLUTE_URL));
            
            if (isset($response['errorCode'])) {
                $errorMessage = $response['errorMessage'];
            } else {
                $order->setOrderId($response['orderId']);
                $this->em->flush();
                if ($data['sendCheck']) {
                    $session->set('pdf', true);
                }

                return new RedirectResponse($response['formUrl']);
            }
        }

        return $this->render('company/pay.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'errorMessage' => $errorMessage
        ]);
    }

    protected function getDiscountForCurrentUser(Request $request)
    {
        if ($this->getUser()) return 0;

        if (!$request->cookies->has(ConstantHelper::DISCOUNT_COOKIE_KEY))
            return 0;

        list($status, $value, $maxCount, $used) = $this->em->getRepository(Config::class)->findBy(['name' => [ConstantHelper::DISCOUNT_STATUS_KEY, ConstantHelper::DISCOUNT_VALUE_KEY, ConstantHelper::DISCOUNT_MAX_COUNT_KEY, ConstantHelper::DISCOUNT_USED_KEY]]);

        if (!$status->getValue()) return 0;

        if ($maxCount->getValue() != 0 && $maxCount->getValue() <= $used->getValue())
            return 0;

        $discountValue = $value->getValue();
        return $discountValue < 0 ? 0 : ($discountValue > 100 ? 100 : (int) $discountValue);

    }

    public function order2Action(Product $product, Request $request, Session $session)
    {
        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);
        $errorMessage = '';

        if (!$product->isProductActive()) {
            return $this->redirectToRoute('homepage');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $order = new Order();
            $order->makeCode()->setEmail($data['email'])
                ->setTotal($product->getPriceWithDiscount($this->getDiscountForCurrentUser($request)))
                ->setStatus(Order::STATUS_NEW)
                ->setProduct($product)
                ->setWithDiscount($this->hasDiscount($request));
            $this->em->persist($order);
            $this->em->flush();

            $price = $order->getPriceForPayment();
            $response = $this->get('app.payment')->makePayment($order, $data['email'], $price, $product->getName());

            if (isset($response['errorCode'])) {
                $errorMessage = $response['errorMessage'];
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['errors' => ['payment' => $errorMessage], 'success' => false]);
                }
            } else {
                $order->setOrderId($response['orderId']);
                $this->em->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['redirectUrl' => $response['formUrl'], 'success' => true]);
                }

                return new RedirectResponse($response['formUrl']);
            }
        } else {
            if ($request->isXmlHttpRequest()) {
                $arr = ['email', 'accept'];
                $errors = [];
                foreach ($arr as $item) {
                    $fieldError = $form->get($item)->getErrors(true)->current();
                    if ($fieldError) {
                        $errors[$item] = $fieldError->getMessage();
                    }
                }
                return new JsonResponse(['errors' => $errors, 'success' => false]);
            }
        }

        return $this->render('company/pay.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'errorMessage' => $errorMessage
        ]);
    }

    public function homepageAction(Request $request, Session $session)
    {

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, ['constraints' => [new ThreeSecretsNotUsed()]])->getForm();
        $form->handleRequest($request);
        $paymentErrors = $session->get('paymentErrors');
        if ($paymentErrors) {
            $session->remove('paymentErrors');
        }

        if ($form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            $threeSecrets = new ThreeSecrets();
            $threeSecrets->setEmail($data['email']);

            $siteUrl = $this->getParameter('site_url');
            $content = $this->renderView('mail/3_secrets.html.twig', [
                'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
                'siteUrl' => $siteUrl
            ]);
            $this->get('helpers.mailer')->send('7 веских причин', $data['email'],
                $content
            );

            $this->sendInfoEmail(' Новый подписчик формы "7 причин"', "Новый подписчик " . $data['email'] . " формы \"7 причин\" был успешно добавлен.");

            $this->em->persist($threeSecrets);
            $this->em->flush();

            $this->addFlash('success', 'E-mail ' . $threeSecrets->getEmail() . ' добавлен.');

            return $this->redirectToRoute('homepage');
        }

        $landingPage = $this->em->getRepository(LandingPage::class)->find(1);

        
        return $this->render('company/homepage_final.html.twig', [
            'commonQuestions' => $this->em->getRepository(CommonQuestion::class)->findBy([], ['id' => 'ASC']),
            'products' => $this->em->getRepository(Product::class)->findAll(),
            'feedbacks' => $this->em->getRepository(Feedback::class)->findAll(),
            'form' => $form->createView(),
            'discount' => $this->getDiscountForCurrentUser($request),
            'landingPage' => $landingPage
        ]);

    }

    public function subscribeFormAction(Session $session)
    {
        $request = Request::createFromGlobals();
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscribeType::class, $subscriber);
        $form->handleRequest($request);

        $errorKey = 'subscribeError';
        $successKey = 'subscribeSuccess';

        $message = $error = '';
        if ($session->has($successKey)) {
            $message = $session->get($successKey);
            $session->remove($successKey);
        }
        if ($session->has($errorKey)) {
            $error = $session->get($errorKey);
            $session->remove($errorKey);
        }

        $referrer = $request->headers->get('referer');
        $redirectUrl = $referrer ?? $this->generateUrl('homepage');

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($subscriber);
                $this->em->flush();

                $session->set($successKey, 'Почта ' . $subscriber->getEmail() . ' была добавлена в список рассылки');

                $siteUrl = $this->getParameter('site_url');
                $content = $this->renderView('mail/about-challange_letter.html.twig', [
                    'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
                    'siteUrl' => $siteUrl,
                    'companyPage' => $this->em->getRepository(CompanyPage::class)->findOneBy(['slug' => ConstantHelper::ABOUT_PAGE_SLUG])
                ]);

                $this->get('helpers.mailer')->send('Подписка на рассылку 28dney.ru', $subscriber->getEmail(), $content);

                } else {
                    $errors = $form->getErrors();
                    $message = $form->get('email')->getErrors()->current()->getMessage();
                    $session->set($errorKey, $message);
                }

            return $this->redirect($redirectUrl);
        }

        $showSubscribeForm = true;

        $form->get('email')->addError(new FormError('some error'));

        return $this->render('company/partial/subscribe_form.html.twig', [
            'form' => $form->createView(),
            'showSubscribeForm' => $showSubscribeForm,
            'message' => $message,
            'formError' => $error
        ]);
    }


    public function paymentFailAction(Request $request)
    {
        $view = 'company/payment_fail.html.twig';
        $params = [
            'title' => $request->request->get('Message', 'Платёж не прошел')
        ];

        return $this->render($view,
            $params
        );
    }


    public function testAction()
    {
//        dump('test'); die;

        $order = 77224147;
        $res = $this->get('app.payment')->isSuccessOrderTinkoff($order);
        dump($res); die;
    }

    public function paymentFinishAction(Request $request, Session $session)
    {
        //Must have GET parameter OrderId and it's must be equals to code
        $order = $this->em->getRepository(Order::class)->findOneBy([
            'code' => $request->get('OrderId', 0)
        ]);
        
        if (!$order || !$order->isNew()) {
            return $this->redirectToRoute('homepage');
        }

        if (!$this->get('app.payment')->isSuccessOrderTinkoff($order->getOrderId())) {
            return $this->render('company/success.html.twig', [
                'success' => false
            ]);
        }

        $firstTime = false;
        if (!$this->getUserByOrder($order)) {
            $firstTime = true;
            $this->registerNewUser($order);
        } else {
            $this->processNewOrder($order);
        }

        $response = $this->render('company/success.html.twig', [
            'success' => true,
            'firstTime' => $firstTime
        ]);

        //Скидка
        if ($this->hasDiscount($request)) {
            $response->headers->clearCookie(ConstantHelper::DISCOUNT_COOKIE_KEY);
            $used = $this->em->getRepository(Config::class)->findOneBy(['name' => ConstantHelper::DISCOUNT_USED_KEY]);
            $used->setValue($used->getValue() + 1);
            $this->em->flush();
        }

        return $response;
    }

    protected function hasDiscount(Request $request)
    {
        return $request->cookies->has(ConstantHelper::DISCOUNT_COOKIE_KEY);
    }

    protected function getUserByOrder(Order $order)
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $order->getEmail()]);
    }


    protected function processNewOrder(Order $order)
    {
        $user = $this->getUserByOrder($order);
        $oldOrder = $this->em->getRepository(Order::class)->findOneBy([
            'email' => $user->getEmail(),
            'status' => Order::STATUS_SUCCESS
        ]);

        $oldOrder->setStatus(Order::STATUS_OLD)
            ->clearUser();


        $user->setProduct($order->getProduct())
            ->setStarted(new \DateTime())
            ->resetDays();
        $this->em->flush();

        $order->setStatus(Order::STATUS_SUCCESS)
            ->setUser($user);
        $this->em->flush();

        $product = $order->getProduct();

        $this->get('helpers.mailer')->send('Покупка курса ' . $product->getName() . ' в системе 28dney.ru', $user->getEmail(), $this->renderView('mail/course_purchase.html.twig', [
            'product' => $product,
            'url' => $this->generateUrl('account_day_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'confUrl' => $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti'], UrlGeneratorInterface::ABSOLUTE_URL)
        ]));

        $days = $product->getProductDays();
        $day = $days[$user->getDayNumber() - 1];
        $siteUrl = $this->getParameter('site_url');

        $this->get('helpers.mailer')->send('День #' . $user->getDayNumber(), $user->getEmail(),
            $this->renderView('mail/day_new.html.twig', [
                'day' => $day,
                'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
                'dayUrl' => $siteUrl . $this->generateUrl('account_day', ['id' => $day->getId()]),
                'siteUrl' => $siteUrl
            ])
        );

        $this->sendInfoEmail('Уведомление об оплате', "Оплата курса ({$product->getName()}) произведена успешно");
    }

    protected function registerNewUser(Order $order)
    {
        $product = $order->getProduct();
        $user = new User();
        $user->setEmail($order->getEmail())->setRoles([User::MINIMAL_ROLE])
            ->setHash($this->get('app.string_helper')->generateHash())
            ->setPassword('')
            ->setProduct($product);
        $this->em->persist($user);
        $order->setStatus(Order::STATUS_SUCCESS)
            ->setUser($user);

        $this->em->flush();

        $content = $this->renderView('mail/register.html.twig', [
            'user' => $user,
            'url' => $this->generateUrl('signup_email', ['hash' => $user->getHash()], UrlGeneratorInterface::ABSOLUTE_URL),
            'confUrl' => $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti'], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);

        $this->get('helpers.mailer')->send('Регистрация в системе 28dney.ru', $user->getEmail(), $content);

        $this->sendInfoEmail('Уведомление об оплате',  "Оплата курса ({$product->getName()}) произведена успешно");
    }

    protected function sendInfoEmail($subject, $body)
    {
        $this->get('helpers.mailer')->send($subject, $this->getInfoEmail(), $body);
    }

    public function cityAction(Request $request)
    {
        $city = $request->get('term');
        $cities = $this->em->getRepository(City::class)->gitCitiesByName($city);
        $data = [];
        foreach ($cities as $city) {
            $data[] = ['text' => $city, 'id' => $city];
        }

        return new JsonResponse(['results' => $data]);
    }


    public function companyPageAction(CompanyPage $companyPage)
    {
        return $this->render('company/company_page.html.twig', [
            'companyPage' => $companyPage
        ]);
    }

    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }

    public function pdfAction(Order $order)
    {
        $payment = $this->get('app.payment');
        $orderId = $order->getOrderId();
        $status = $payment->getState($orderId);

        if (!$payment->isSuccessOrderTinkoff($orderId)) {
            throw new BadRequestHttpException('Произшла ошибка попробуйте позже');
        }

        

        $expired = substr($status['expiration'], -2 ) . '.' . substr($status['expiration'], 0, 4);

        $pdf = (new PdfCreator())->createOrder($status['Pan'], $order->getCreated()->format('d.m.Y H:i:s'), $status['OrderNumber'], $expired, $status['cardholderName'], $order->getTotal(), $status['approvalCode']);

        return new Response($pdf, 200, ['Content-Type' => 'application/pdf']);
    }

    public function aboutUsAction(Request $request)
    {
        $feedbacks = $this->em->getRepository(Feedback::class)->findBy([], ['id' => "DESC"], 3);
        
        return $this->render('company/aboutUs.html.twig', [
            'feedbacks' => $feedbacks
        ]);
    }

    public function howItWorksAction(Request $request)
    {
        return $this->render('company/howItWorks.html.twig', [
        ]);
    }

    public function aboutChallengeAction(Request $request)
    {
        $companyPage = $this->em->getRepository(CompanyPage::class)->findOneBy(['slug' => ConstantHelper::ABOUT_PAGE_SLUG]);

        return $this->render('company/aboutChallenge.html.twig', [
            'page' => $companyPage
        ]);
    }

    public function createDiscountAction(Request $request)
    {
        $response = new RedirectResponse($this->generateUrl('homepage'));

        list($status, $count, $expired, $used) = $this->em->getRepository(Config::class)->findBy(['name' =>
            [
                ConstantHelper::DISCOUNT_STATUS_KEY,
                ConstantHelper::DISCOUNT_EXPIRED_KEY,
                ConstantHelper::DISCOUNT_MAX_COUNT_KEY,
                ConstantHelper::DISCOUNT_USED_KEY
            ] ], ['id' => 'ASC']);

        if (
            !$this->getUser() &&
            !$request->cookies->has(ConstantHelper::DISCOUNT_COOKIE_KEY) &&
            $status->getValue() &&
            (!$count->getValue() || $count->getValue() > $used->getValue())) {

            $expired = $expired->getValue() ? time() + 86400 * $expired->getValue() : 0;
            $cookie = new Cookie(ConstantHelper::DISCOUNT_COOKIE_KEY, 1, $expired);
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
