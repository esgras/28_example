<?php

namespace AppBundle\Command\Mail;

use AppBundle\Entity\Common\ThreeSecrets;
use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
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

class SendDiscounts extends ContainerAwareCommand
{

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:send-discounts')
            ->setDescription('Send Discounts for emails')
            ->setHelp('Send Discounts for emails...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $mailer = $this->getContainer()->get('helpers.mailer');
        $subject = '28 дней скидка';
        $emails = ['esgras@ukr.net', 'vaynnorg@gmail.com', 'yanagolovko90@gmail.com'];
        $siteUrl = $this->getContainer()->getParameter('site_url');

        $content = $this->getContainer()->get('twig')->render('mail/mailing/discount.html.twig', [
            'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
            'discountUrl' => $siteUrl . $this->generateUrl('create_discount')
        ]);


        foreach ($emails as $email) {
                $mailer->send($subject, $email, $content);
        }

        $this->forceSendEmail();


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