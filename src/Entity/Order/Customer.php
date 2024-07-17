<?php

namespace App\Entity\Order;

use App\Repository\Order\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customers')]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(name: 'page_id')]
    private int|null $pageId = null;

    #[ORM\Column(name: 'facebook_profile_id', length: 255, nullable: true)]
    private string|null $facebookProfileId = null;

    #[ORM\Column(name: 'full_name', length: 100, nullable: true)]
    private string|null $fullName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private string|null $email = null;

    #[ORM\Column(name: 'mobile_number', length: 11, nullable: true)]
    private string|null $mobileNumber = null;

    #[ORM\Column(name: 'address_line', length: 255, nullable: true)]
    private string|null $addressLine = null;    

    #[ORM\Column(name: 'region', length: 255, nullable: true)]
    private string|null $region = null;  

    #[ORM\Column(name: 'province', length: 255, nullable: true)]
    private string|null $province = null;  

    #[ORM\Column(name: 'city', length: 255, nullable: true)]
    private string|null $city = null;  

    #[ORM\Column(name: 'barangay', length: 255, nullable: true)]
    private string|null $barangay = null;  

    #[ORM\Column(name: 'zip_code', length: 10, nullable: true)]
    private string|null $zipCode = null;  

    public function __construct(
        int|null $pageId = null,
        string|null $facebookProfileId = '',
        string|null $fullName = '',
        string|null $email = '',
        string|null $mobileNumber = '',
        string|null $addressLine = '',
        string|null $region = '',
        string|null $province = '',
        string|null $city = '',
        string|null $barangay = '',
        string|null $zipCode = '',
    ) {
        $this->pageId = $pageId;
        $this->facebookProfileId = $facebookProfileId;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->mobileNumber = $mobileNumber;
        $this->addressLine = $addressLine;
        $this->region = $region;
        $this->province = $province;
        $this->city = $city;
        $this->barangay = $barangay;
        $this->zipCode = $zipCode;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'pageId' => $this->getPageId(),
            'facebookProfileId' => $this->getFacebookProfileId(),
            'fullName' => $this->getFullName(),
            'email' => $this->getEmail(),
            'mobileNumber' => $this->getMobileNumber(),
            'addressLine' => $this->getAddressLine(),
            'region' => $this->getRegion(),
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'barangay' => $this->getBarangay(),
            'zipCode' => $this->getZipCode(),

        ];
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFacebookProfileId(): ?string
    {
        return $this->facebookProfileId;
    }

    public function setFacebookProfileId(string $facebookProfileId): static
    {
        $this->facebookProfileId = $facebookProfileId;

        return $this;
    }    

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

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

    public function getAddressLine(): ?string
    {
        return $this->addressLine;
    }

    public function setAddressLine(?string $addressLine): static
    {
        $this->addressLine = $addressLine;

        return $this;
    } 
    
    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): static
    {
        $this->province = $province;

        return $this;
    }  

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }
    
    public function getBarangay(): ?string
    {
        return $this->barangay;
    }

    public function setBarangay(?string $barangay): static
    {
        $this->barangay = $barangay;

        return $this;
    }          

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }          

}
