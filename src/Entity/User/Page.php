<?php

namespace App\Entity\User;

use App\Repository\User\PageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'pages')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(name: 'user_id')]
    private int|null $userId = null;

    #[ORM\Column(name: 'facebook_page_id', length: 255, nullable: true)]
    private string|null $facebookPageId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $name = null;

    #[ORM\Column(name: 'facebook_page_access_token', nullable: true)]
    private string|null $facebookPageAccessToken = null;

    #[ORM\Column(nullable: true)]
    private array|null $metadata = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTime|null $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTime|null $updatedAt = null;


    public function __construct(
        int|null $userId = null,
        string|null $facebookPageId = '',
        string|null $name = '',
        string|null $facebookPageAccessToken = '',
        array|null $metadata = []
    ) {
        $this->userId = $userId;
        $this->facebookPageId = $facebookPageId;
        $this->name = $name;
        $this->facebookPageAccessToken = $facebookPageAccessToken;
        $this->metadata = $metadata;
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'facebookPageId' => $this->getFacebookPageId(),
            'name' => $this->getName(),
            'facebookPageAccessToken' => $this->getFacebookPageAccessToken(),
            'metadata' => $this->getMetadata(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getFacebookPageId(): ?string
    {
        return $this->facebookPageId;
    }

    public function setFacebookPageId(string $facebookPageId): static
    {
        $this->facebookPageId = $facebookPageId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFacebookPageAccessToken(): ?string
    {
        return $this->facebookPageAccessToken;
    }

    public function setFacebookPageAccessToken(?string $facebookPageAccessToken): static
    {
        $this->facebookPageAccessToken = $facebookPageAccessToken;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;

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
        return $this->createdAt->format('M d, Y h:i A');
    }            
}
