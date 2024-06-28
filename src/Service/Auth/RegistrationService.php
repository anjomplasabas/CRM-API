<?php

namespace App\Service\Auth;

use App\DTO\Request\Auth\RegistrationDTO;
use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Enum\UserType;
use App\Repository\User\ProfileRepository;
use App\Repository\User\UserRepository;
use App\Service\User\ProfileService;
use App\Service\User\UserService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
