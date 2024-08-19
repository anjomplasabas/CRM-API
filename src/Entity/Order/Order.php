<?php

namespace App\Entity\Order;

use App\Entity\Order\Customer;
use App\Entity\User\Page;
use App\Repository\Order\OrderRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;    

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int|null $userId = null;

    #[ORM\Column(name: 'page_id')]
    private int|null $pageId = null;

    #[ORM\Column]
    private int|null $customerId = null;

    #[ORM\Column(length: 255)]
    private string|null $referenceNumber = null;

    #[ORM\Column]
    private float|null $totalAmount = null;

    #[ORM\Column]
    private float|null $grandTotalAmount = null;

    #[ORM\Column(length: 50)]
    private string|null $status = null;

    #[ORM\Column(nullable: true)]
    private array|null $metadata = null;

    #[ORM\OneToOne(
        targetEntity: Customer::class,
    )]
    #[ORM\JoinColumn(
        name: 'customer_id',
        referencedColumnName: 'id'
    )]
    #[Ignore]
    private Customer|null $customer = null;

    #[ORM\OneToOne(
        targetEntity: Page::class,
    )]
    #[ORM\JoinColumn(
        name: 'page_id',
        referencedColumnName: 'id'
    )]
    #[Ignore]
    private Page|null $page = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTime|null $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTime|null $updatedAt = null;

    public function __construct(
        int|null $userId,
        int|null $pageId = null,
        int|null $customerId,
        string|null $referenceNumber,
        float|null $totalAmount,
        float|null $grandTotalAmount,
        string|null $status,
        array|null $metadata,
        Customer|null $customer = null,
        Page|null $page = null
    ) {
        $this->userId = $userId;
        $this->pageId = $pageId;
        $this->customerId = $customerId;
        $this->referenceNumber = $referenceNumber;
        $this->totalAmount = $totalAmount;
        $this->grandTotalAmount = $grandTotalAmount;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->customer = $customer;
        $this->page = $page;
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'pageId' => $this->getPageId(),
            'customerId' => $this->getCustomerId(),
            'referenceNumber' => $this->getReferenceNumber(),
            'totalAmount' => $this->getTotalAmount(),
            'grandTotalAmount' => $this->getGrandTotalAmount(),
            'status' => $this->getStatus(),
            'metadata' => $this->getMetadata(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUserId(): int|null
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    public function setPageId(int $pageId): static
    {
        $this->pageId = $pageId;

        return $this;
    }
    public function getCustomerId(): int|null
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getTotalAmount(): float|null
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getGrandTotalAmount(): float|null
    {
        return $this->grandTotalAmount;
    }

    public function setGrandTotalAmount(int $grandTotalAmount): static
    {
        $this->gradTotalAmount = $grandTotalAmount;

        return $this;
    }

    public function getReferenceNumber(): string|null
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): static
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getStatus(): string|null
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMetadata(): array|null
    {
        return $this->metadata;
    }

    public function setMetadata(array|null $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getCustomer(): Customer|null
    {
        return $this->customer;
    }

    public function setCustomer(Customer|null $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getPage(): Page|null
    {
        return $this->page;
    }

    public function setPage(Page|null $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getCreatedAt(): DateTime|null
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime|null $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime|null
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime|null $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->createdAt->format('M d, Y h:i: A'); // Customize the format as needed
    }    
}
