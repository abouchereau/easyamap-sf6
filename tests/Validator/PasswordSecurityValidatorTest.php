<?php

namespace App\Tests\Validator;

use App\Validator\ContainsAlphanumeric;
use App\Validator\ContainsAlphanumericValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ContainsAlphanumericValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new PasswordSecurityValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new PasswordSecurity());

        $this->buildViolation('myMessage')
            ->assertRaised();
    }

    /**
    * @dataProvider provideInvalidConstraints
    */
    public function testTrueIsInvalid(PasswordSecurity $constraint): void
    {
        $this->validator->validate('Azerty123*', $constraint);

        $this->buildViolation('myMessage')
        ->assertRaised();
    }

    public function provideInvalidConstraints(): \Generator
    {
        yield [new PasswordSecurity(message: 'aze')];
    // ...
    }
}