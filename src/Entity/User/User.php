<?php

namespace App\Entity\User;

use App\Repository\User\UserRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private string|null $type = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(name: 'auth_id', length: 255, unique: true)]
    private string|null $authId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $password = null;

    #[ORM\Column(name: 'is_forgot', type: Types::BOOLEAN, nullable: true)]
    private bool|null $isForgot = null;

    #[ORM\Column(length: 50, nullable: true)]
    private string|null $status = null;

    #[ORM\OneToOne(
        targetEntity: Profile::class,
        mappedBy: 'user',
    )]
    private Profile|null $profile = null;

    #[ORM\OneToOne(
        targetEntity: PasswordResetToken::class,
        mappedBy: 'user',
    )]
    private PasswordResetToken|null $passwordResetToken = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTime|null $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTime|null $updatedAt = null;

    public function __construct(
        string|null $type = null,
        array $roles = [],
        string $authId = '',
        string|null $password = '',
        bool|null $isForgot = false,
        string $status = '',
        Profile|null $profile = null,
        PasswordResetToken|null $passwordResetToken = null
    ) {
        $this->type = $type;
        $this->roles = $roles;
        $this->authId = $authId;
        $this->password = $password;
        $this->isForgot = $isForgot;
        $this->status = $status;
        $this->profile = $profile;
        $this->passwordResetToken = $passwordResetToken;
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'roles' => $this->getRoles(),
            'authId' => $this->getAuthId(),
            'isForgot' => $this->isForgot(),
            'status' => $this->getStatus(),
            'profile' => $this->getProfile(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getAuthId(): ?string
    {
        return $this->authId;
    }

    public function setAuthId(string $authId): static
    {
        $this->authId = $authId;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isForgot(): ?bool
    {
        return $this->isForgot;
    }

    public function setForgot(?bool $isForgot): static
    {
        $this->isForgot = $isForgot;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getPasswordResetToken(): ?PasswordResetToken
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?PasswordResetToken $passwordResetToken): static
    {
        $this->passwordResetToken = $passwordResetToken;

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

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->authId;
    }
}
