<?php

namespace AppBundle\Security\Common;

use AppBundle\Entity\Day;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DayVoter extends Voter
{
    const VIEW = 'day_view';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW])) {
            return false;
        }

        if (!$subject instanceof Day) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $subject);
        }

        throw new \LogicException('This code should not be reached');
    }

    protected function canView(User $user, Day $day)
    {
        $product = $user->getProduct();
//        dump($product->getId());
//        dump($day->getProduct()->getId());
//        die;

        return $day->getProduct()->getId() == $product->getId() &&
            $user->getDayNumber() >= $day->getNumber();
    }

}