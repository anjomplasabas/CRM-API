<?php

namespace App\DTO\Request\Auth;

use App\Enum\UserType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class RegistrationDTO
{

    public function __construct(
        #[
            Assert\NotNull(message: 'User must have role assigned.'),
            Assert\NotBlank(message: 'User role must not be null'),
            Assert\Type(
                type: 'array'
            )
        ]
        public array $roles,
        #[
            Assert\NotNull(message: 'User must have an assigned type.'),
            Assert\NotBlank(message: 'User type must not be blanked.'),
            Assert\Type(
                type: 'string'
            )
        ]
        public string $type,
        #[
            Assert\NotNull(message: 'User must have a valid Auth ID.'),
            Assert\NotBlank(message: 'Auth ID must not be blanked.'),
            Assert\Type(
                type: 'string'
            )
        ]
        public string $authId,
        #[
            Assert\When(
                expression: 'this.shouldValidatePassword()',
                constraints: [
                    new Assert\NotBlank(
                        message: 'Password must not be blanked.'
                    ),
                    new Assert\PasswordStrength([
                        'minScore' => Assert\PasswordStrength::STRENGTH_WEAK
                    ]),
                    new Assert\Type(
                        type: 'string'
                    )
                ]
            )
        ]
        public string|null $password,
        #[
            Assert\Length(
                min: 4,
                max: 25
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $firstName,
        #[
            Assert\Length(
                min: 4,
                max: 25
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $lastName,
        #[
            Assert\When(
                expression: 'this.shouldValidateEmail()',
                constraints: [
                    new Assert\NotBlank(
                        message: 'Email must not be blanked.'
                    ),
                    new Assert\Email(message: 'Must be a valid email address.')
                ]
            )
        ]
        public string|null $email,
        #[
            Assert\When(
                expression: 'this.shouldValidateMobileNumber()',
                constraints: [
                    new Assert\NotBlank(
                        message: 'Mobile number must not be blanked.'
                    ),
                    new Assert\Length(
                        min: 10,
                        max: 11
                    ),
                    new Assert\Type(
                        type: 'string'
                    )
                ]
            )
        ]
        public string|null $mobileNumber
    ) {
    }

    public function shouldValidateEmail(): bool
    {
        return $this->type == UserType::GOOGLE->name;
    }

    public function shouldValidateMobileNumber(): bool
    {
        return $this->type == UserType::EMAIL->name;
    }

    public function shouldValidatePassword(): bool
    {
        return UserType::canAuthenticateByPassword($this->type);
    }
}
