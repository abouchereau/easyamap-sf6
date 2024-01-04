<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordSecurityValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordSecurity) {
            throw new UnexpectedTypeException($constraint, PasswordSecurity::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        // access your configuration options like this:
        if ('strict' === $constraint->mode) {
            // ...
        }

        if ($this->_mdpIsValid($value)) {
            return;
        }

        // the argument must be a string or an object implementing __toString()
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }

    private function _mdpIsValid(?string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }
        if ($password == strtolower($password)) {
            return false;
        }
        if ($password == strtoupper($password)) {
            return false;
        }
        if (!preg_match('~[0-9]+~', $password)) {
            return false;
        }
        if (!preg_match('/[^a-zA-Z\d]/', $password)) {
            return false;
        }
        return true;
    }
}