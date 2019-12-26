<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EmailExists extends Constraint
{
    public $message = 'Email "{{ string }}" doesn\'t exist';
}