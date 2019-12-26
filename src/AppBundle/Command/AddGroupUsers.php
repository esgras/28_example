<?php

namespace AppBundle\Command;

use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Order;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\VarDumper\VarDumper;

class AddGroupUsers extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:add-group-users')
            ->setDescription('Add users from control group')
//            ->setHelp('Send Letter with Day Information...')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $mailer = $this->getContainer()->get('helpers.mailer');
        $stringHelper = $this->getContainer()->get('app.string_helper');
        $siteUrl = $this->getContainer()->getParameter('site_url');

        $users = ['russtudinger@gmail.com'];


        $product = $this->em->getRepository(Product::class)->find(1);

        foreach ($users as $email) {
            $order = new Order();
            $order->makeCode()->setEmail($email)
                ->setTotal($product->getPrice())->setStatus(Order::STATUS_NEW)
                ->setProduct($product);
            $this->em->persist($order);
            $this->em->flush();


            $user = new User();
            $user->setEmail($email)->setRoles([User::MINIMAL_ROLE])
                ->setHash($stringHelper->generateHash())
                ->setPassword('')
                ->setProduct($product);
            $this->em->persist($user);
            $order->setStatus(Order::STATUS_SUCCESS)
                ->setUser($user);

//            $user->addDay();

            $this->em->flush();

            $content = $this->getContainer()->get('twig')->render('mail/register.html.twig', [
                'user' => $user,
                'url' => $siteUrl . $this->generateUrl('signup_email', ['hash' => $user->getHash()], UrlGeneratorInterface::ABSOLUTE_PATH),
                'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti'], UrlGeneratorInterface::ABSOLUTE_PATH)
            ]);

            $mailer->send('Регистрация в системе 28dney.ru', $user->getEmail(), $content);

        }


        $this->forceSendEmail();
        $this->em->flush();


        echo 'Letters was sended', PHP_EOL;
        die;
    }

    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->getContainer()->get('router')->generate($route, $parameters, $referenceType);
    }

    public function forceSendEmail()
    {
        $mailer = $this->getContainer()->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->getContainer()->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }


}