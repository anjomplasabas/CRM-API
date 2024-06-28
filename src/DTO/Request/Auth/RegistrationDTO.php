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
            Assert\NotNull(message: 'User must have a valid email.'),
            Assert\NotBlank(message: 'Email must not be blanked.'),
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
            Assert\NotNull(message: 'User must have a valid first name.'),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $firstName,
        #[
            Assert\NotNull(message: 'User must have a valid last name.'),
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
            Assert\NotBlank(
                message: 'Mobile number must not be blanked.'
            ),
            Assert\Length(
                min: 10,
                max: 11,
                minMessage: 'Mobile number format is invalid.',
                maxMessage: 'Mobile number format is invalid.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $mobileNumber,
        #[
            Assert\Type(
                type: 'boolean'
            )
        ]
        public bool|null $isForgot,
    ) {
    }

    public function shouldValidateEmail(): bool
    {
        return $this->type == UserType::GOOGLE->name;
    }

    public function shouldValidatePassword(): bool
    {
        return UserType::canAuthenticateByPassword($this->type);
    }

    public function toArray(): array
    {
        return [
            'roles' => $this->roles,
            'type' => $this->type,
            'authId' => $this->authId,
            'password' => $this->password,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'mobileNumber' => $this->mobileNumber,
            'isForgot' => $this->isForgot,
        ];
    }
}
