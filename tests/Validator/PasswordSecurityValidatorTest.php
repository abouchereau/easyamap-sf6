<?php
namespace App\Tests\Validator;

use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use App\Validator\PasswordSecurityValidator;
use App\Validator\PasswordSecurity;

class PasswordSecurityValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new PasswordSecurityValidator();
    }

    public function testPasswordValid(): void
    {
        $this->validator->validate("Azerty321*", new PasswordSecurity('strict'));
        $this->assertNoViolation();
    }

    public function testPasswordNotValid1(): void
    {
        $this->validator->validate("azerty321*", new PasswordSecurity('strict'));
        $this->buildViolation("Le mot de passe doit respecter les exigences de sécurité.")->assertRaised();
    }

    public function testPasswordNotValid2(): void
    {
        $this->validator->validate("Azerty321", new PasswordSecurity('strict'));
        $this->buildViolation("Le mot de passe doit respecter les exigences de sécurité.")->assertRaised();
    }
}