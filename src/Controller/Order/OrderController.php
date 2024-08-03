<?php

namespace App\Controller\Order;

use App\DTO\Request\Order\OrderDTO;
use App\Repository\Order\OrderRepository;
use App\Repository\Order\ReportTemplateRepository;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/order')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderService $orderService,
        string $baseDir,
        private readonly ReportTemplateRepository $reportTemplateRepository,
    )
    {
        $this->baseDir = $baseDir;
    }

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

    #[Route('/templates', name: 'app_show_template_report', methods: ['GET'])]
    public function showTemplates(        
        #[MapQueryParameter] int|null $pageId,
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $pageSize = 10,
        #[MapQueryParameter] string|null $searchQuery = null
    ): JsonResponse
    {
        try {
            $reportTemplatesQuery = $this->reportTemplateRepository->findByPageId(
                $pageId,
                $page,
                $pageSize,
                $searchQuery
            );

            return $this->json($reportTemplatesQuery);
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
    
    #[Route('/import-template/{pageId}', name: 'app_import_template_report', methods: ['POST'])]
    public function import(Request $request, int $pageId) :JsonResponse
    {
        $file = $request->files->get('template');
        $name = $request->request->get('name');      
          
        // Move template to upload directory
        $uploadDirectory = $this->baseDir . '/var/orders/report-templates/'.$pageId;
        $templateFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->guessExtension();
        $file->move($uploadDirectory, $templateFileName);

        // New File Path
        $filePath = $uploadDirectory . '/' . $templateFileName;
        // Create Template
        $template = $this->orderService->createTemplate($filePath, $templateFileName, $name, $pageId, $this->getUser()?->getId());

        return $this->json([
            'data' => $template
        ]);;
    }

    #[Route('/export/{templateId}/{pageId}', name: 'app_export_report', methods: ['GET'])]
    public function export(
        int $templateId, 
        int $pageId,
        #[MapQueryParameter] string|null $dateFrom,
        #[MapQueryParameter] string|null $dateTo,
    ) :BinaryFileResponse
    {
        // Export Report
        $data = $this->orderService->processReport($templateId, $pageId, $this->baseDir, $dateFrom, $dateTo);

        $response = new BinaryFileResponse($data['filePath']);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $data['fileName']);

        return $response;
    }  
}
