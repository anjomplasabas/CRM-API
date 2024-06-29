<?php

namespace App\DTO\Request\Organization;

use App\Enum\UserType;
use Symfony\Component\Validator\Constraints as Assert;

class OrganizationMemberDTO
{

    public function __construct(
        #[
            Assert\NotNull(
                message: 'User must have a valid first name.'
            ),
            Assert\NotBlank(
                message: 'First name must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $firstName,
        #[
            Assert\NotNull(
                message: 'User must have a valid last name.'
            ),
            Assert\NotBlank(
                message: 'Last name must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $lastName,
        #[
            Assert\NotBlank(
                message: 'Email must not be blanked.'
            ),
            Assert\Email(
                message: 'Must be a valid email address.'
            )
        ]
        public string|null $email,
        #[
            Assert\NotBlank(
                message: 'Password must not be blanked.'
            ),
            Assert\PasswordStrength([
                'minScore' => Assert\PasswordStrength::STRENGTH_WEAK
            ]),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $password,
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
    ) {
    }

}