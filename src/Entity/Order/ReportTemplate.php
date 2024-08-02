<?php

namespace App\Entity\Order;

use App\Repository\Order\ReportTemplateRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;    

#[ORM\Entity(repositoryClass: ReportTemplateRepository::class)]
#[ORM\Table(name: 'report_templates')]
class ReportTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int|null $userId = null;

    #[ORM\Column]
    private int|null $pageId = null;

    #[ORM\Column(length: 255)]
    private string|null $fileName = null;

    #[ORM\Column]
    private array|null $headers = null;

    #[ORM\Column]
    private array|null $parameters = null;

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
        int|null $pageId,
        string|null $fileName,
        array|null $headers,
        array|null $parameters
    ) {
        $this->userId = $userId;
        $this->pageId = $pageId;
        $this->fileName = $fileName;
        $this->headers = $headers;
        $this->parameters = $parameters;
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'pageId' => $this->getPageId(),
            'fileName' => $this->getFileName(),
            'headers' => $this->getHeaders(),
            'parameters' => $this->getParameters(),
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

    public function getPageId(): int|null
    {
        return $this->pageId;
    }

    public function setPageId(int $pageId): static
    {
        $this->pageId = $pageId;

        return $this;
    }

    public function getFileName(): string|null
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileNamegeId = $fileName;

        return $this;
    }

    public function getHeaders(): array|null
    {
        return $this->headers;
    }

    public function setHeaders(array|null $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function getParameters(): array|null
    {
        return $this->parameters;
    }

    public function setParameters(array|null $parameters): static
    {
        $this->parameters = $parameters;

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
}