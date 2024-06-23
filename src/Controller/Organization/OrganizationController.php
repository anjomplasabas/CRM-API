<?php

namespace App\Controller\Organization;

use App\Entity\Organization\Organization;
use App\Repository\Organization\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/organizations')]
class OrganizationController extends AbstractController
{

    public function __construct(
        private readonly OrganizationRepository $organizationRepository
    ) {}

    #[Route('/', name: 'app_organization_show_by_user', methods: ['GET'])]
    public function showByUser(): JsonResponse
    {
        $organization = $this->organizationRepository->findOneBy([
            'userId' => $this->getUser()?->getId()
        ]);

        return $this->json([
            'data' => $organization
        ]);
    }

    #[Route('/store', name: 'app_organization_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $parameters = $request->toArray();
        if (!isset($parameters['name']) || $parameters['name'] === '') {
            return $this->json([
                'message' => 'Organization name cannot be blank.'
            ], 400);
        }
        $organization = $this->organizationRepository->findOneBy([
            'userId' => $this->getUser()->getId()
        ]);
        if ($organization) {
            return $this->json([
                'message' => 'There\'s an existing organization for this user already.'
            ], 400);
        }
        $organization = new Organization(
            userId: $this->getUser()?->getId(),
            name: $parameters['name'],
            status: 'ACTIVE'
        );
        $this->organizationRepository->save($organization);

        return $this->json([
            'data' => $organization
        ]);
    }
}
