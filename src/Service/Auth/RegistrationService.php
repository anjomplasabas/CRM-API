<?php

namespace App\Service\Auth;

use App\DTO\Request\Auth\RegistrationDTO;
use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Repository\User\ProfileRepository;
use App\Repository\User\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class RegistrationService
{

    public function __construct(
        private readonly ProfileRepository $profileRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function handle(RegistrationDTO $registrationDTO): User|null
    {
        $user = new User(
            type: $registrationDTO->type,
            roles: $registrationDTO->roles,
            authId: $registrationDTO->authId,
            isForgot: $registrationDTO->isForgot,
            status: 'PENDING'
        );
        if ($registrationDTO->shouldValidatePassword()) {
            $password = $this->passwordHasher->hashPassword($user, $registrationDTO->password);
            $user->setPassword($password);
        }
        $this->userRepository->save($user);
        $profile = new Profile(
            userId: $user->getId(),
            firstName: $registrationDTO->firstName,
            lastName: $registrationDTO->lastName,
            email: $registrationDTO->email,
            mobileNumber: $registrationDTO->mobileNumber,
            user: $user
        );
        $this->profileRepository->save($profile);

        $user->setProfile($profile);
        $this->userRepository->save($user);

        return $this->userRepository->find($user->getId());
    }
}
