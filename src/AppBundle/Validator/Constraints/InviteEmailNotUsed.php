<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class InviteEmailNotUsed extends Constraint
{
    public $message = 'Email "{{ string }}" уже получил приглашение';
}