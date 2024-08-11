<?php

namespace App\Controller\Order;

use App\DTO\Request\Order\OrderDTO;
use App\Repository\Order\OrderRepository;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderService $orderService
    )
    {}

    #[Route('/order', name: 'app_orders_by_pageid', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] int|null $pageId,
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $pageSize = 10,
        #[MapQueryParameter] string|null $searchQuery = null
    ): JsonResponse
    {
        try {
            $ordersQuery = $this->orderRepository->findByPageId(
                $pageId,
                $page,
                $pageSize,
                $searchQuery
            );

            return $this->json($ordersQuery);
        } catch (\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);
        }
    }

    #[Route('/order/{orderId}', name: 'app_show_order_details', methods: ['GET'])]
    public function show(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderRepository->find($orderId);

            return $this->json([
                'data'=> $order
            ]);
        } catch(\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);            
        }
    }

    #[Route('/order/store', name: 'app_create_order', methods: ['POST'])]
    public function store(#[MapRequestPayload]  OrderDTO $orderDTO): JsonResponse
    {           
        $order = $this->orderService->handle($orderDTO);

        return $this->json([
            'data' => $order
        ]);     
    }

    #[Route('/order/update-status/{orderId}', name: 'app_update_order_status', methods: ['PUT'])]
    public function updateStatus(int $orderId, Request $request): JsonResponse
    {
        $parameters = $request->toArray();
        
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return $this->json([
                'message' => 'Order does not exist.'
            ], 404);
        }
        $order->setStatus($parameters['status']);
        $this->orderRepository->save($order);

        return $this->json([
            'data' => $order
        ]);
    }

}
