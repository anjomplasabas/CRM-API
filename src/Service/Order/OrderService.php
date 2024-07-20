<?php

namespace App\Service\Order;

use App\Entity\Order\Customer;
use App\Entity\Order\Order;
use App\DTO\Request\Order\OrderDTO;
use App\Repository\Order\OrderRepository;
use App\Repository\Order\CustomerRepository;

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
}