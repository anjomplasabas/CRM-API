<?php

namespace App\Controller\User;

use App\Entity\Profile\Profile;
use App\DTO\Request\User\ProfileDTO;
use App\Repository\User\ProfileRepository;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository $profileRepository,
        private readonly UserRepository $userRepository
    )
    {}

    #[Route('/profile', name: 'app_profile_by_user', methods: ['GET'])]
    public function showByUser(): JsonResponse
    {
        $profile = $this->profileRepository->findProfileByUser($this->getUser()?->getId());
        return $this->json([
            'data' => $profile
        ]);
    }

    #[Route('/profile/update', name: 'app_update_profile_by_user', methods: ['PUT'])]
    public function updateProfile(#[MapRequestPayload] ProfileDTO $profileDTO): JsonResponse
    {
        $user = $this->getUser();
        $profile = $this->profileRepository->updateProfile($user->getId(), $profileDTO);
        // Update user status
        if ($profileDTO->status) {
            $user->setStatus($profileDTO->status);
            $this->userRepository->save($user);
        }

        return $this->json([
            'data' => $profile
        ]);
    }    
}
