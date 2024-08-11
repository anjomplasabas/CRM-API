<?php

namespace App\Controller\Order;

use App\Repository\Order\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api')]
class CustomerController extends AbstractController
{
    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}

    #[Route('/customer', name: 'app_customers_by_pageid', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] int|null $pageId,
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $pageSize = 10,
        #[MapQueryParameter] string|null $searchQuery = null
    ): JsonResponse
    {
        try {
            $customersQuery = $this->customerRepository->findByPageId(
                $pageId,
                $page,
                $pageSize,
                $searchQuery
            );

            return $this->json($customersQuery);
        } catch (\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);
        }
    }

    #[Route('/customer/{customerId}', name: 'app_show_customer_details', methods: ['GET'])]
    public function show(int $customerId): JsonResponse
    {
        try {
            $customer = $this->customerRepository->find($customerId);

            return $this->json([
                'data'=> $customer
            ]);
        } catch(\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);            
        }
    }
 
}
