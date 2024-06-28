<?php

namespace App\Entity\Organization;

use App\Entity\User\User;
use App\Repository\Organization\OrganizationMemberRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationMemberRepository::class)]
#[ORM\Table(name: 'organization_members')]
class OrganizationMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int|null $organizationId = null;

    #[ORM\Column]
    private int|null $userId = null;

    #[ORM\Column(length: 50)]
    private string|null $status = null;

    #[ORM\Column(nullable: true)]
    private array|null $metadata = null;

    #[ORM\OneToOne(
        targetEntity: User::class,
        inversedBy: 'organizationMember',
    )]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id'
    )]
    private User|null $user = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTimeImmutable|null $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTimeImmutable|null $updatedAt = null;

    public function __construct(
        int|null $organizationId,
        int|null $userId,
        string|null $status,
        array|null $metadata,
    ) {
        $this->organizationId = $organizationId;
        $this->userId = $userId;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'organizationId' => $this->getOrganizationId(),
            'userId' => $this->getUserId(),
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

    public function getOrganizationId(): int|null
    {
        return $this->organizationId;
    }

    public function setOrganizationId(int $organizationId): static
    {
        $this->organizationId = $organizationId;

        return $this;
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

    public function getCreatedAt(): DateTimeImmutable|null
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable|null $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable|null
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable|null $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
