<?php

namespace AppBundle\Command;

use AppBundle\Entity\CommonQuestion;
use AppBundle\Entity\Day;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use AppBundle\Traits\CourseTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\VarDumper\VarDumper;

class SendDayLetter extends ContainerAwareCommand
{
    use CourseTrait;

    /** @var EntityManager $em */
    private $em;

    protected function configure()
    {
        $this->setName('app:send-day-letter')
            ->setDescription('Send Letter with Day Information')
            ->setHelp('Send Letter with Day Information...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $mailer = $this->getContainer()->get('helpers.mailer');
        $users = $this->em->getRepository(User::class)->getUsersWithProductsAndDays();
        $waitingUsers = $this->em->getRepository(User::class)->getWaitingForStart();
        $siteUrl = $this->getContainer()->getParameter('site_url');
        $twig = $this->getContainer()->get('twig');
        
        foreach ($users as $user) {
            if ($user->canAddDay()) {
                $user->addDay();
                if ($user->checkTemplatesForProduct()) {
                    $days = $user->getProduct()->getProductDays();
                    $day = $days[$user->getDayNumber() - 1];

                    $view = $twig->render('mail/day_new.html.twig', [
                        'day' => $day,
                        'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
                        'dayUrl' => $siteUrl . $this->generateUrl('account_day', ['id' => $day->getId()]),
                        'siteUrl' => $siteUrl
                    ]);
                    $mailer->send('День #' . $user->getDayNumber(),
                        $user->getEmail(), $view);
                }
            }
        }

        $now = (new \DateTime())->setTime(0, 0, 0);
        $currentYear = $now->format('Y');
        foreach ($waitingUsers as $waitingUser) {
            $productMonth = $waitingUser->getProduct()->getMonth();
            if (!$productMonth) continue;

            $startDate = new \DateTime("{$currentYear}-{$productMonth}-01");
            if ($now == $startDate) {
                $this->startCourse($this->em, $mailer, $twig, $siteUrl, $waitingUser);
            }
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