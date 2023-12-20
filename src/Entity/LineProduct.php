<?php

namespace App\Entity;

use App\Repository\LineProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineProductRepository::class)]
class LineProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $unitPrice = null;

    #[ORM\ManyToOne(inversedBy: 'lineProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $lineProductOrder = null;

    #[ORM\ManyToOne(inversedBy: 'productLine')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $productLine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = sprintf("%.2f",$unitPrice);

        return $this;
    }

    public function getLineProductOrder(): ?Order
    {
        return $this->lineProductOrder;
    }

    public function setLineProductOrder(?Order $lineProductOrder): static
    {
        $this->lineProductOrder = $lineProductOrder;

        return $this;
    }

    public function getProductLine(): ?Product
    {
        return $this->productLine;
    }

    public function setProductLine(?Product $productLine): static
    {
        $this->productLine = $productLine;

        return $this;
    }
}
