<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Day;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInvite;
use AppBundle\Form\Type\InviteFriendType;
use AppBundle\Security\Common\DayVoter;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AccountController extends BaseController
{
    public function indexAction(Request $request)
    {
        return $this->render('main/account/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    public function dayListAction($page = 1, Request $request, Session $session)
    {
        /** @var Product $product */
        $product = $this->getUser()->getProduct();
        $limit = 28; // 4 Weeks per page
        $daysInWeek = 7;
        
        $days = $this->em->getRepository(Day::class)->findBy(['product' => $product], ['number' => 'ASC'], $product->getDays());
        $daysForPagination = $this->get('knp_paginator')->paginate($days, $page, $limit);
        $weeksCount = ceil(count($days) /$daysInWeek);
        $weeksPerPage = ceil($limit / $daysInWeek);

        $form = $this->createForm(InviteFriendType::class);
        $form->handleRequest($request);

        $email = false;
        if ($email = $session->get('inviteSuccess')) {
            $session->remove('inviteSuccess');
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $session->set('inviteSuccess', $email);
            $this->inviteFriend($this->getUser(), $email);

            return $this->redirect($request->getRequestUri());
        }

        return $this->render('main/account/day_list.html.twig', [
            'days' => $days,
            'daysCount' => count($days),
            'view_rule' => DayVoter::VIEW,
            'daysForPagination' => $daysForPagination,
            'page' => $page,
            'limit' => $limit,
            'currentDays' => array_slice($days, ($page - 1) * $limit, $limit),
            'daysInWeek' => 7,
            'weeksCount' => $weeksCount,
            'startWeek' => ($page - 1) * $weeksPerPage,
            'weeksPerPage' => $weeksPerPage,
            'form' => $form->createView(),
            'email' => $email
        ]);

    }

    protected function inviteFriend(User $user, string $email): void
    {
        $userInvite = new UserInvite();
        $userInvite->setEmail($email)->setAuthor($user);

        $siteUrl = $this->getParameter('site_url');
        $content = $this->renderView('mail/invite-friend_letter.html.twig', [
            'confUrl' => $siteUrl . $this->generateUrl('company_page', ['slug' => 'politika-konfidentsialnosti']),
            'siteUrl' => $siteUrl
        ]);

        $this->get('helpers.mailer')->send('Приглашение на 28dney.ru', $email, $content);

        $this->em->persist($userInvite);
        $this->em->flush();
    }

    public function dayAction(Day $day, Request $request)
    {
        $this->denyAccessUnlessGranted( DayVoter::VIEW, $day);
        $referer = $request->headers->get('referer');
        $backUrl = $referer && preg_match('#/account/day-list#', $referer) ? $referer :
            $this->generateUrl('account_day_list');

        return $this->render('main/account/day.html.twig', [
            'day' => $day,
            'backUrl' => $backUrl
        ]);
    }
}