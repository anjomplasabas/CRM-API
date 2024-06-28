<?php

namespace App\Service\User;

use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Enum\UserType;
use App\Repository\User\ProfileRepository;

class ProfileService
{

    public function __construct(
        private readonly ProfileRepository $profileRepository
    ) {}

    public function save(array $profileData, User $user): Profile
    {
        $profile = new Profile(
            userId: $user->getId(),
            firstName: $profileData['firstName'],
            lastName: $profileData['lastName'],
            email: $profileData['email'],
            mobileNumber: $profileData['mobileNumber'],
            user: $user
        );
        $this->profileRepository->save($profile);

        return $profile;
    }

}