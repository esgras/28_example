<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\City;
use AppBundle\Entity\Config;
use AppBundle\Entity\Order;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\Type\Auth\PasswordResetType;
use AppBundle\Form\Type\SignupType;
use AppBundle\Helper\ConstantHelper;
use AppBundle\Traits\CourseTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

class AuthController extends BaseController
{
    use CourseTrait;

    public function signupAction(string $hash, Request $request)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['hash' => $hash]);
        if (!$user) {
            return $this->redirectToRoute('homepage');
        }

        if (!($user->hasStartedChallenge() || $user->getisWaitingForStart())) {
            // For first product
            if ($user->getProduct()->getId() == ConstantHelper::PRODUCT_MAIN_ID) {
                $this->startCourse($this->em, $this->get('helpers.mailer'), $this->get('twig'),
                        $this->getParameter('site_url'), $user);
            } else {
                $user->setIsWaitingForStart(true);
            }
            $this->em->flush();
        }
        //START ORDER

        $form = $this->createForm(SignupType::class, $user, [
            'cities' => $this->em->getRepository(City::class)->getCitiesList()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT))
                ->setStatus(User::STATUS_ACTIVE)
                ->setHash(null);
            $this->em->persist($user);
            $this->em->flush();

            $this->loginUser($user);

            $this->get('helpers.mailer')->send('Уведомление об регистрации',
                $this->getInfoEmail(),
                "Регистрация пользователя ({$user->getEmail()}) на сайте успешна");

            return $this->redirectToRoute('account_day_list');
        }

        return $this->render('auth/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function loginAction(AuthenticationUtils $helper, Request $request)
    {
        return $this->render("auth/login_new.html.twig", [
            'lastUsername' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    public function logoutAction()
    {

    }

    public function passwordResetAction(Request $request)
    {
        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->em->getRepository(User::class)
                ->findOneBy(['email' => $form->getData()['email']]);

            if ($user->isNotActive()) {
                $order = $this->em->getRepository(Order::class)->findOneBy(['user' => $user]);
                if (!$order) {
                    throw new BadRequestHttpException('There was an error, try later');
                }

                $this->get('helpers.mailer')->send('Восстановление пароля', $user->getEmail(),
                    $this->renderView('mail/register.html.twig', [
                        'user' => $user,
                        'url' => $this->generateUrl('signup_email', ['hash' => $user->getHash()], UrlGeneratorInterface::ABSOLUTE_URL),
                        'confUrl' => $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti'], UrlGeneratorInterface::ABSOLUTE_URL),
                    ])
                );
            } else {
                $hashAndPassword = $this->get('app.string_helper')->getPasswordAndHash($user);
                $user->setPassword($hashAndPassword['hash']);
                $this->em->flush();

                $this->get('helpers.mailer')->send('Восстановление пароля', $user->getEmail(),
                    $this->renderView('mail/pass.html.twig', [
                        'password' => $hashAndPassword['password'],
                        'confUrl' => $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti'], UrlGeneratorInterface::ABSOLUTE_URL),
                    ])
                );
            }

            $this->addFlash('success', 'Проверьте почту, чтобы изменить пароль.');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('auth/password_reset_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function confirmAction()
    {
        return $this->render('auth/mail_confirmation.html.twig');
    }

}