<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $extendedAddress = null;

    #[ORM\Column(length: 20)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    private ?string $country = null;

    #[ORM\ManyToOne(targetEntity:AddressCategory::class, inversedBy:"addresses")]
    #[ORM\JoinColumn(nullable: false)]
    private ?AddressCategory $addressCategory = null;

    #[ORM\OneToMany(targetEntity:Order::class, mappedBy:"invoiceAddress")]
    private $invoiceOrders;

    #[ORM\OneToMany(targetEntity:Order::class, mappedBy:"deliveryAddress")]
    private $deliveryOrders;

    #[ORM\ManyToOne(inversedBy: 'userAddress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userAddress = null;


    public function __construct()
    {
        $this->invoiceOrders = new ArrayCollection();
        $this->deliveryOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getExtendedAddress(): ?string
    {
        return $this->extendedAddress;
    }

    public function setExtendedAddress(?string $extendedAddress): static
    {
        $this->extendedAddress = $extendedAddress;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAddressCategory(): ?AddressCategory
    {
        return $this->addressCategory;
    }

    public function setAddressCategory(?AddressCategory $addressCategory): static
    {
        $this->addressCategory = $addressCategory;

        return $this;
    }

        /**
     * @return Collection|Order[]
     */
    public function getInvoiceOrders(): Collection
    {
        return $this->invoiceOrders;
    }

    public function addInvoiceOrder(Order $invoiceOrder): static
    {
        if (!$this->invoiceOrders->contains($invoiceOrder)) {
            $this->invoiceOrders[] = $invoiceOrder;
            $invoiceOrder->setInvoiceAddress($this);
        }

        return $this;
    }

    public function removeInvoiceOrder(Order $invoiceOrder): static
    {
        if ($this->invoiceOrders->removeElement($invoiceOrder) && $invoiceOrder->getInvoiceAddress() === $this) {
            // set the owning side to null (unless already changed)
            $invoiceOrder->setInvoiceAddress(null);
        }

        return $this;
    }

    public function getDeliveryOrders(): Collection
    {
        return $this->deliveryOrders;
    }

    public function addDeliveryOrder(Order $deliveryOrder): static
    {
        if (!$this->deliveryOrders->contains($deliveryOrder)) {
            $this->deliveryOrders[] = $deliveryOrder;
            $deliveryOrder->setDeliveryAddress($this);
        }

        return $this;
    }

    public function removeDeliveryOrder(Order $deliveryOrder): static
    {
        if ($this->deliveryOrders->removeElement($deliveryOrder) && $deliveryOrder->getDeliveryAddress() === $this) {
            // set the owning side to null (unless already changed)
            $deliveryOrder->setDeliveryAddress(null);
        }

        return $this;
    }

    public function getUserAddress(): ?User
    {
        return $this->userAddress;
    }

    public function setUserAddress(?User $userAddress): static
    {
        $this->userAddress = $userAddress;

        return $this;
    }

}
