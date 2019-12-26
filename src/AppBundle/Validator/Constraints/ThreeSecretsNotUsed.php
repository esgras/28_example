<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ThreeSecretsNotUsed extends Constraint
{
    public $message = 'Email "{{ string }}" уже используется';
}