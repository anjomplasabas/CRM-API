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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Route('api/order')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderService $orderService
    )
    {}

    #[Route('/', name: 'app_orders_by_pageid', methods: ['GET'])]
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

    #[Route('/{orderId}', name: 'app_show_order_details', methods: ['GET'])]
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

    #[Route('/store', name: 'app_create_order', methods: ['POST'])]
    public function store(#[MapRequestPayload]  OrderDTO $orderDTO): JsonResponse
    {           
        $order = $this->orderService->handle($orderDTO);

        return $this->json([
            'data' => $order
        ]);     
    }

    #[Route('/update-status/{orderId}', name: 'app_update_order_status', methods: ['PUT'])]
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
    
    #[Route('/import/{pageId}', name: 'app_import_orders', methods: ['POST'])]
    public function import(Request $request, int $pageId) :JsonResponse
    {
        $file = $request->files->get('orders');
        $data = $this->orderService->exportReport($file->getPathname(), $pageId, $this->getUser()?->getId());
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $reportHeaders = array_values($data['headers']);
        $sheet->fromArray($reportHeaders, NULL, 'A1');
        $sheet->fromArray($data['data'], NULL, 'A2');

        // Save the file
        $fileName = 'exported-orders-' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $filePath = $this->getParameter('kernel.project_dir') . '/var/exports/' . $fileName;
        $writer->save($filePath);
        
        return $this->json([
            'data' => $fileName
        ]);
    }
}
