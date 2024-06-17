<?php

namespace App\DTO\Request\Auth;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdatePasswordDTO
{

    public function __construct(
        #[
            Assert\NotBlank(message: 'Password token invalid'),
        ]
        public string $token,
        #[
            Assert\PasswordStrength([
                'minScore' => Assert\PasswordStrength::STRENGTH_WEAK
            ]),
        ]
        public string $password
    ) {}
}