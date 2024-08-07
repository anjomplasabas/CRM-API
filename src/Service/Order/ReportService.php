<?php

namespace App\Service\Order;

use App\Entity\Order\Order;
use App\Entity\Order\ReportTemplate;
use App\Repository\Order\OrderRepository;
use App\Repository\Order\ReportTemplateRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

readonly class ReportService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ReportTemplateRepository $reportTemplateRepository
    ) {
    }

    public function createTemplate(
        string $filePath,
        string $fileName, 
        string $name,
        int $pageId, 
        int $userId
    ): mixed
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $cells = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            if ($rowIndex == 2) {
                $parameters = $cells;
                break;
            }
            $headers = $cells;            
        }

        $template = new ReportTemplate(
            pageId: $pageId,
            userId: $userId,
            name: $name,
            fileName: $fileName,
            headers: $headers,
            parameters: $parameters
        );
        $this->reportTemplateRepository->save($template);

        return $template;
    }

    public function processReport(
        int $templateId, 
        int $pageId, 
        string $baseDir, 
        string $dateFrom, 
        string $dateTo
    ): array|null
    {
        $iterator = 0;
        $orders = $this->orderRepository->findByPageIdAndDateRange($pageId, $dateFrom, $dateTo);
        $template = $this->reportTemplateRepository->find($templateId);
        $parameters = $template->getParameters();
        $headers = $template->getHeaders();

        foreach ($orders as $order) {
            foreach ($parameters as $parameter) {
                $methodName = 'get' . ucfirst($parameter);
                
                if (method_exists($order, $methodName)) {
                    $data[$iterator][] =  $order->$methodName();
                } elseif($parameter == 'products') {
                    $data[$iterator][] = $order->getMetadata()['product'];
                } elseif(method_exists($order->getCustomer(), $methodName)) {
                    $data[$iterator][] = $order->getCustomer()->$methodName();
                } else {
                    $data[$iterator][] = $parameter;
                }
            }
            $iterator++;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $reportHeaders = array_values($headers);
        $sheet->fromArray($reportHeaders, NULL, 'A1');
        if(!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        // Save the file
        $fileName = 'exported-orders-' . uniqid() . '.xlsx';
        $directory = $baseDir . '/var/orders/exports/' . $pageId;

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filePath = $baseDir . '/var/orders/exports/' . $pageId . '/' . $fileName;
        $writer->save($filePath);

        return [
            'filePath' => $filePath,
            'fileName' => $fileName
        ];
    }
}