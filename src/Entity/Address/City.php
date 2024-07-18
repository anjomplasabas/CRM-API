<?php

namespace App\Entity\Address;

use App\Repository\Address\CityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(name: 'cities')]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'province_id')]
    private ?int $provinceId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'name_en', length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(nullable: true)]
    private ?array $metadata = null;

    public function __construct(
        int|null $provinceId = null,
        string|null $name = null,
        string|null $nameEn = null,
        array|null $metadata = null
    ) {
        $this->provinceId = $provinceId;
        $this->name = $name;
        $this->nameEn = $nameEn;
        $this->metadata = $metadata;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvinceId(): ?int
    {
        return $this->provinceId;
    }

    public function setProvinceId(int $provinceId): static
    {
        $this->provinceId = $provinceId;

        return $this;
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
