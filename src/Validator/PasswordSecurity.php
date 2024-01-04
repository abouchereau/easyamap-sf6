<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PasswordSecurity extends Constraint
{
    public string $message = 'Le mot de passe doit respecter les exigences de sécurité.';
    public string $mode = 'strict';

    // all configurable options must be passed to the constructor
    public function __construct(string $mode = null, string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->mode = $mode ?? $this->mode;
        $this->message = $message ?? $this->message;
    }
}

//https://symfony.com/doc/6.4/validation/custom_constraint.html