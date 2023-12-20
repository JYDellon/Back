<?php

namespace App\Entity;

use App\Repository\LineCartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineCartRepository::class)]
class LineCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(targetEntity:Cart::class, inversedBy:"cartLineCarts")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;

    #[ORM\ManyToOne(inversedBy: 'lineCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $lineCartProduct = null;

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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }

    public function getLineCartProduct(): ?Product
    {
        return $this->lineCartProduct;
    }

    public function setLineCartProduct(?Product $lineCartProduct): static
    {
        $this->lineCartProduct = $lineCartProduct;

        return $this;
    }
}
