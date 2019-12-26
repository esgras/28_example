<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EmailNotUsed extends Constraint
{
    public $message = 'Email "{{ string }}" уже используется';
}