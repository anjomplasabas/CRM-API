<?php

namespace App\Service\Customer;

use App\DTO\Request\Customer\CustomerDTO;
use App\Entity\Customer\Customer;
use App\Repository\Customer\CustomerRepository;

readonly class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository
    ) {
    }

    public function handle(CustomerDTO $customerDTO): Customer|null
    {
        $customer = new Customer(
            pageId: $customerDTO->pageId,
            facebookProfileId: $customerDTO->facebookProfileId,
            fullName: $customerDTO->fullName,
            mobileNumber: $customerDTO->mobileNumber,
            email: $customerDTO->email,
            addressLine: $customerDTO->addressLine,
            region: $customerDTO->region,
            province: $customerDTO->province,
            city: $customerDTO->city,
            barangay: $customerDTO->barangay,
            zipCode: $customerDTO->zipCode
        );
        $this->customerRepository->save($customer);
        return $this->customerRepository->find($customer->getId());
    }
}