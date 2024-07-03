<?php

namespace App\Controller\Auth;

use App\DTO\Request\Auth\RegistrationDTO;
use App\Repository\Organization\OrganizationRepository;
use App\Service\Auth\RegistrationService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly OrganizationRepository $organizationRepository,
        private readonly RegistrationService $registrationService,
    ) {}

    #[Route('/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(#[CurrentUser] $user): JsonResponse
    {
        $organization = $this->organizationRepository->findOneBy([
            'userId' => $user->getId(),
        ]);

        return $this->json([
            'data' => [
                'token' => $this->tokenManager->createFromPayload($user, [
                    'firstName' => $user->getProfile()->getFirstName(),
                    'lastName' => $user->getProfile()->getLastName(),
                    'organizationId' => $organization?->getId()
                ])
            ]
        ]);
    }

    #[Route('/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegistrationDTO $registrationDTO): JsonResponse
    {
        try {
            $user = $this->registrationService->handle($registrationDTO);

            return $this->json([
                'data' => [
                    'token' => $this->tokenManager->createFromPayload($user, [
                        'firstName' => $user->getProfile()->getFirstName(),
                        'lastName' => $user->getProfile()->getLastName(),
                        'organizationId' => null
                    ])
                ]
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            return $this->json([
                'message' => 'User email already exists.'
            ]);
        }
    }
}
