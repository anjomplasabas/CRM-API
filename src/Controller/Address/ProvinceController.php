<?php

namespace App\Controller\Address;

use App\Repository\Address\ProvinceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/addresses')]
class ProvinceController extends AbstractController
{
    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
    ) {}

    #[Route('/provinces', name: 'app_address_province')]
    public function index(): JsonResponse
    {
        $provinces = $this->provinceRepository->findAll();

        return $this->json([
            'data' => $provinces
        ]);
    }
}
