<?php

namespace App\Controller\Organization;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/members')]
class OrganizationMemberController extends AbstractController
{
    #[Route('/', name: 'app_organization_organization_member')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Organization/OrganizationMemberController.php',
        ]);
    }

    public function store(): JsonResponse
    {
        return $this->json([]);
    }
}
