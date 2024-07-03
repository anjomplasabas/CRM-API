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
        public string|null $firstName
    ) {  
    }

}