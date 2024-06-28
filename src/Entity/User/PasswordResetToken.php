<?php

namespace App\Entity\User;

use App\Repository\User\PasswordResetTokenRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasswordResetTokenRepository::class)]
#[ORM\Table('password_reset_tokens')]
class PasswordResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(name: 'user_id', unique: false)]
    private int|null $userId = null;

    #[ORM\Column(length: 100)]
    private string|null $email = null;

    #[ORM\Column(length: 255)]
    private string|null $token = null;

    #[ORM\Column(length: 25)]
    private string|null $status = null;

    #[ORM\OneToOne(
        targetEntity: User::class,
        inversedBy: 'passwordResetToken',
    )]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id'
    )]
    private User|null $user = null;

    #[ORM\Column(name: 'expired_at', type: Types::DATETIME_MUTABLE)]
    private DateTimeImmutable|null $expiredAt = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTimeImmutable|null $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true, options: [
        'default' => 'CURRENT_TIMESTAMP'
    ])]
    private DateTimeImmutable|null $updatedAt = null;

    public function __construct(
        int|null $userId = null,
        string|null $email = null,
        string|null $token = null,
        string|null $status = null,
        User|null $user = null,
        DateTimeImmutable|null $expiredAt = null
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->token = $token;
        $this->status = $status;
        $this->user = $user;
        $this->expiredAt = $expiredAt;
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'email' => $this->email,
            'token' => $this->token,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): User|null
    {
        return $this->user;
    }

    public function setUser(User|null $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiredAt(): DateTimeImmutable|null
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(DateTimeImmutable|null $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
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
