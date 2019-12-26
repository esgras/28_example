<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailNotUsedValidator extends ConstraintValidator
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

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $value]);

        if ($user) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

    }
}