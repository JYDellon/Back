<?php

namespace App\Entity;

use App\Repository\ReturnedProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReturnedProductRepository::class)]
class ReturnedProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $returnedDate = null;

    #[ORM\Column(length: 255)]
    private ?string $returnedObject = null;

    #[ORM\Column(length: 255)]
    private ?string $returnedStatus = null;

    #[ORM\ManyToOne(inversedBy: 'orderReturnedProduct')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderReturnedProduct = null;

    #[ORM\ManyToOne(inversedBy: 'productReturned')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $productReturned = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReturnedDate(): ?\DateTimeInterface
    {
        return $this->returnedDate;
    }

    public function setReturnedDate(\DateTimeInterface $returnedDate): static
    {
        $this->returnedDate = $returnedDate;

        return $this;
    }

    public function getReturnedObject(): ?string
    {
        return $this->returnedObject;
    }

    public function setReturnedObject(string $returnedObject): static
    {
        $this->returnedObject = $returnedObject;

        return $this;
    }

    public function getReturnedStatus(): ?string
    {
        return $this->returnedStatus;
    }

    public function setReturnedStatus(string $returnedStatus): static
    {
        $this->returnedStatus = $returnedStatus;

        return $this;
    }

    public function getOrderReturnedProduct(): ?Order
    {
        return $this->orderReturnedProduct;
    }

    public function setOrderReturnedProduct(?Order $orderReturnedProduct): static
    {
        $this->orderReturnedProduct = $orderReturnedProduct;

        return $this;
    }

    public function getProductReturned(): ?Product
    {
        return $this->productReturned;
    }

    public function setProductReturned(?Product $productReturned): static
    {
        $this->productReturned = $productReturned;

        return $this;
    }
}
