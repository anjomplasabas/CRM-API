<?php

namespace App\Service\Order;

use App\DTO\Request\Order\OrderDTO;
use App\Entity\Order\Customer;
use App\Repository\Order\CustomerRepository;

readonly class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository
    ) {
    }

    public function handle(OrderDTO $orderDTO): Customer|null
    {
    }
}