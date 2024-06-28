<?php

namespace App\Service\User;

use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Enum\UserType;
use App\Repository\User\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ) {}

    public function save(array $userData): User
    {
        $user = new User(
            type: $userData['type'],
            roles: $userData['roles'],
            authId: $userData['authId'],
            isForgot: $userData['isForgot'],
            status: $userData['status'] ?? 'ACTIVE'
        );
        if (UserType::canAuthenticateByPassword($userData['type'])) {
            $password = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($password);
        }
        $this->userRepository->save($user);

        return $user;
    }

    public function addProfile(User $user, Profile $profile): User
    {
        $user->setProfile($profile);
        $this->userRepository->save($user);

        return $user;
    }

}