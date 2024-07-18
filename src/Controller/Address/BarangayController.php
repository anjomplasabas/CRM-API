<?php

namespace App\Controller\Address;

use App\Repository\Address\BarangayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/addresses')]
class BarangayController extends AbstractController
{
    public function __construct(
        private readonly BarangayRepository $barangayRepository
    ) {}

    #[Route('/barangays/{cityId}', name: 'app_addresses_barangay')]
    public function index(
        #[MapQueryParameter] int|null $cityId
    ): JsonResponse
    {
        $barangays = $this->barangayRepository->findBy(['cityId' => $cityId]);
        return $this->json([
            'data' => $barangays
        ]);
    }
}
