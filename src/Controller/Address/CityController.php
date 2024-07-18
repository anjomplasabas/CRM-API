<?php

namespace App\Controller\Address;

use App\Repository\Address\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/addresses')]
class CityController extends AbstractController
{
    public function __construct(
        private readonly CityRepository $cityRepository
    ) {}

    #[Route('/cities/{provinceId}', name: 'app_addresses_city')]
    public function index(
        #[MapQueryParameter] int|null $provinceId
    ): JsonResponse {
        return $this->json([
            'data' => $this->cityRepository->findBy(['provinceId' => $provinceId])
        ]);
    }
}
