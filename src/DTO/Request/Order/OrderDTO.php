<?php

namespace App\DTO\Request\Order;

use Symfony\Component\Validator\Constraints as Assert;

readonly class OrderDTO
{
    public function __construct(
        public int|null $userId,
        public int|null $pageId,
        public string|null $facebookProfileId,    
        #[
            Assert\NotNull(
                message: 'User must have a valid full name.'
            ),
            Assert\NotBlank(
                message: 'full name must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $fullName,
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
            Assert\Email(
                message: 'Must be a valid email address.'
            )
        ]
        public string|null $email,
        #[
            Assert\NotNull(
                message: 'User must have a valid address line.'
            ),
            Assert\NotBlank(
                message: 'address line must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $addressLine,
        #[
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $region,
        #[
            Assert\NotNull(
                message: 'User must have a valid province.'
            ),
            Assert\NotBlank(
                message: 'province must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $province,
        #[
            Assert\NotNull(
                message: 'User must have a valid city.'
            ),
            Assert\NotBlank(
                message: 'city must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $city,
        #[
            Assert\NotNull(
                message: 'User must have a valid barangay.'
            ),
            Assert\NotBlank(
                message: 'barangay must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $barangay,
        #[
            Assert\NotNull(
                message: 'User must have a valid zip code.'
            ),
            Assert\NotBlank(
                message: 'zip code must not be blanked.'
            ),
            Assert\Type(
                type: 'string'
            )
        ]
        public string|null $zipCode,
        #[
            Assert\NotNull(
                message: 'User must have a valid total amount.'
            ),
            Assert\NotBlank(
                message: 'total amount must not be blanked.'
            ),
            Assert\Type(
                type: 'float'
            )
        ]
        public float|null $totalAmount,        
        public array|null $metadata   
    ) {  
    }

}