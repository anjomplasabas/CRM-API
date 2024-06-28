<?php

namespace App\Service\Auth;

use App\DTO\Request\Auth\RegistrationDTO;
use App\Entity\User\User;
use App\Service\User\ProfileService;
use App\Service\User\UserService;

readonly class RegistrationService
{

    public function __construct(
        private readonly ProfileService  $profileService,
        private readonly UserService $userService,
    ) {
    }

    public function handle(RegistrationDTO $registrationDTO): User|null
    {
        $user = $this->userService->save($registrationDTO->toArray());
        $profile = $this->profileService->save($registrationDTO->toArray(), $user);

        return $this->userService->addProfile($user, $profile);
    }
}
