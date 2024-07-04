<?php

namespace App\DTO\Request\User;

use Symfony\Component\Validator\Constraints as Assert;

readonly class ProfileDTO
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
        public string|null $mobileNumber                
    ) {  
    }

}