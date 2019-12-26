<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

trait CourseTrait
{
    protected function startCourse(EntityManagerInterface $em, $mailer, $twig, $siteUrl, User $user)
    {
        if ($user->getisWaitingForStart()) {
            $user->setIsWaitingForStart(false);
        }

        $user->setStarted(new \DateTime());
        $user->addDay();

        $product = $em->getRepository(Product::class)->find($user->getProduct());
        $days = $product->getProductDays();
        $day = $days[$user->getDayNumber() - 1];

        $mailer->send('День #' . $user->getDayNumber(), $user->getEmail(),
            $twig->render('mail/day_new.html.twig', [
                'day' => $day,
                'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
                'dayUrl' => $siteUrl . $this->generateUrl('account_day', ['id' => $day->getId()]),
                'siteUrl' => $siteUrl
            ])
        );
    }
}