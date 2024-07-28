<?php

namespace App\Service\Order;

use App\Entity\Order\Customer;
use App\Entity\Order\Order;
use App\DTO\Request\Order\OrderDTO;
use App\Repository\Order\OrderRepository;
use App\Repository\Order\CustomerRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;

readonly class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly CustomerRepository $customerRepository
    ) {
    }

    public function handle(OrderDTO $orderDTO): Order|null
    {
        $customer = new Customer(
            pageId: $orderDTO->pageId,
            facebookProfileId: $orderDTO->facebookProfileId,
            fullName: $orderDTO->fullName,
            mobileNumber: $orderDTO->mobileNumber,
            email: $orderDTO->email,
            addressLine: $orderDTO->addressLine,
            region: $orderDTO->region,
            province: $orderDTO->province,
            city: $orderDTO->city,
            barangay: $orderDTO->barangay,
            zipCode: $orderDTO->zipCode
        );
        $this->customerRepository->save($customer);
        
        $order = new Order(
            userId: $orderDTO->userId,
            pageId: $orderDTO->pageId,
            customerId: $customer->getId(),
            referenceNumber: time(),
            totalAmount: $orderDTO->totalAmount,
            grandTotalAmount: $orderDTO->totalAmount,
            status: 'PENDING',
            metadata: $orderDTO->metadata,
            customer: $customer
        );
        $this->orderRepository->save($order);
        
        return $order;
    }
    
    public function exportReport(string $filePath, int $pageId, int $userId): array|null
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $orders = $this->orderRepository->findBy(['pageId' => $pageId]);
        $iterator = 0;

        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
    
            foreach ($cellIterator as $cell) {
                $headers[] = $cell->getValue();
            }
        }

        foreach ($orders as $order) {
            foreach ($headers as $header) {
                $methodName = 'get' . ucfirst($header);

                if (method_exists($order, $methodName)) {
                    $data[$iterator][] =  $order->$methodName();
                } elseif($header == 'products') {
                    $data[$iterator][] = $order->getMetadata()['product'];
                } else {
                    $data[$iterator][] = $order->getCustomer()->$methodName();
                }
            }
            $iterator++;
        }

        return [
            "headers" => $headers,
            "data" => $data
        ];
    }

}