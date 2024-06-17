<?php

namespace App\DataFixtures;

use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Enum\UserType;
use App\Repository\User\ProfileRepository;
use App\Repository\User\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(
        private readonly ProfileRepository $profileRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository
    ) {}

    public function load(ObjectManager $manager): void
    {
        $types = [
            1 => UserType::EMAIL,
            2 => UserType::MOBILE_NUMBER,
            3 => UserType::GOOGLE
        ];

        $authIds = [
            1 => 'test@mailinator.com',
            2 => '09657997580',
            3 => 'asd0asd8a90sdsadasdsad'
        ];

        $profiles = [
            1 => [
                'firstName' => 'Test 1',
                'lastName' => 'User 1',
                'email' => 'test@mailinator.com',
                'mobileNumber' => '09657997580',
            ],
            2 => [
                'firstName' => 'Test 2',
                'lastName' => 'User 2',
                'email' => 'test2@mailinator.com',
                'mobileNumber' => '09657997580'
            ],
            3 => [
                'firstName' => 'Test 3',
                'lastName' => 'User 3',
                'email' => 'test3@mailinator.com',
                'mobileNumber' => '09657997580'
            ]
        ];

        for ($i = 1; $i <= 3; $i++) {

            $user = new User(
                type: $types[$i]->name,
                roles: ['ROLE_SELLER'],
                authId: $authIds[$i]
            );

            if ($types[$i]->name != 'GOOGLE') {
                $password = $this->passwordHasher->hashPassword($user, 'P@ssw0rd123');
                $user->setPassword($password);
            }
            $this->userRepository->save($user);

            $profile = new Profile(
                userId: $user->getId(),
                firstName: $profiles[$i]['firstName'],
                lastName: $profiles[$i]['lastName'],
                email: $profiles[$i]['email'],
                mobileNumber: $profiles[$i]['mobileNumber'],
                user: $user
            );
            $this->profileRepository->save($profile);
        }
    }
}
