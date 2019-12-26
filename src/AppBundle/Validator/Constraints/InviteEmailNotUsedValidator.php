<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\User;
use AppBundle\Entity\UserInvite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InviteEmailNotUsedValidator extends ConstraintValidator
{
    /** @var  EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            $this->context
                ->buildViolation('Email is required')->addViolation();
            return;
        }

        $userInvite = $this->em->getRepository(UserInvite::class)->findOneBy(['email' => $value]);

        if ($userInvite) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

    }
}