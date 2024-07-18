<?php

namespace App\Entity\Address;

use App\Repository\Address\ProvinceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProvinceRepository::class)]
#[ORM\Table(name: 'provinces')]
class Province
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string|null $name = null;

    #[ORM\Column(name: 'name_en',length: 255, nullable: true)]
    private string|null $nameEn = null;

    #[ORM\Column(nullable: true)]
    private array|null $metadata = null;

    public function __construct(
        string|null $name,
        string|null $nameEn,
        array|null $metadata
    ) {
        $this->name = $name;
        $this->nameEn = $nameEn;
        $this->metadata = $metadata;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(?string $nameEn): static
    {
        $this->nameEn = $nameEn;

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
}
