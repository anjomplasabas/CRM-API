<?php

namespace App\Controller\Organization;

use App\DTO\Request\Organization\OrganizationMemberDTO;
use App\Service\Organization\OrganizationMemberService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/members')]
class OrganizationMemberController extends AbstractController
{

    public function __construct(
        private readonly OrganizationMemberService $organizationMemberService
    ) {}

    #[Route('/', name: 'app_organization_members', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Organization/OrganizationMemberController.php',
        ]);
    }

    #[Route('/store', name: 'app_organization_members_store', methods: ['POST'])]
    public function store(#[MapRequestPayload] OrganizationMemberDTO $organizationMemberDTO): JsonResponse
    {
        try {
            $organizationMember = $this->organizationMemberService->handle($organizationMemberDTO);

            return $this->json([
                'data' => $organizationMember
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            return $this->json([
                'message' => 'User email already exists.'
            ]);
        }
    }
}
