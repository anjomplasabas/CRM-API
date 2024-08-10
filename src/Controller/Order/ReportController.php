<?php

namespace App\Controller\Order;

use App\Service\Order\ReportService;
use App\Repository\Order\ReportTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag; 

#[Route('api/templates')]
class ReportController extends AbstractController
{
    public function __construct(
        private readonly ReportTemplateRepository $reportTemplateRepository,
        private readonly ReportService $reportService,
        string $baseDir
    )
    {
        $this->baseDir = $baseDir;
    }

    #[Route('/', name: 'app_show_template_report_list', methods: ['GET'])]
    public function index(        
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
    
    #[Route('/{templateId}', name: 'app_show_template_details', methods: ['GET'])]
    public function show(int $templateId): JsonResponse
    {
        try {
            $template = $this->reportTemplateRepository->find($templateId);

            return $this->json([
                'data'=> $template
            ]);
        } catch(\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);            
        }
    }

    #[Route('/import/{pageId}', name: 'app_import_template_report', methods: ['POST'])]
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
        $template = $this->reportService->createTemplate($filePath, $templateFileName, $name, $pageId, $this->getUser()?->getId());

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
        $data = $this->reportService->processReport($templateId, $pageId, $this->baseDir, $dateFrom, $dateTo);

        $response = new BinaryFileResponse($data['filePath']);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $data['fileName']);

        return $response;
    }      
}