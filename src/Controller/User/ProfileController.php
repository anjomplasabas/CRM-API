<?php

namespace App\Controller\User;

use App\Entity\Profile\Organization;
use App\DTO\Request\Organization\OrganizationMemberDTO;
use App\Repository\User\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/profile')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}

    #[Route('/', name: 'app_profile_by_user', methods: ['GET'])]
    public function showByUser(): JsonResponse
    {
        $profile = $this->profileRepository->findProfileByUser($this->getUser()?->getId());
        return $this->json([
            'data' => $profile
        ]);
    }

    #[Route('/update', name: 'app_profile_by_user', methods: ['PUT'])]
    public function updateProfile(#[MapRequestPayload] OrganizationMemberDTO $organizationMemberDTO): JsonResponse
    {
        dd($organizationMemberDTO);
        $userId = $this->getUser()?->getId();
        $profile = $this->profileRepository->updateProfile($userId, $profileDTO);
        $profile = $this->profileRepository->findProfileByUser($userId);
        return $this->json([
            'data' => $profile
        ]);
    }    
}
