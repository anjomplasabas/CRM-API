<?php

namespace App\Entity\User;

use App\Repository\User\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\Table(name: 'profiles')]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(name: 'user_id')]
    private int|null $userId = null;

    #[ORM\Column(name: 'first_name', length: 50, nullable: true)]
    private string|null $firstName = null;

    #[ORM\Column(name: 'last_name', length: 50, nullable: true)]
    private string|null $lastName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private string|null $email = null;

    #[ORM\Column(name: 'mobile_number', length: 11, nullable: true)]
    private string|null $mobileNumber = null;

    #[ORM\Column(nullable: true)]
    private array|null $metadata = null;

    #[ORM\OneToOne(
        targetEntity: User::class,
        inversedBy: 'profile',
    )]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id'
    )]
    #[Ignore]
    private User|null $user = null;

    public function __construct(
        int|null $userId = null,
        string|null $firstName = '',
        string|null $lastName = '',
        string|null $email = '',
        string|null $mobileNumber = '',
        array|null $metadata = [],
        User|null $user = null
    ) {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->mobileNumber = $mobileNumber;
        $this->metadata = $metadata;
        $this->user = $user;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'email' => $this->getEmail(),
            'metadata' => $this->getMetadata(),
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): static
    {
        $this->mobileNumber = $mobileNumber;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
