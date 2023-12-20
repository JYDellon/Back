<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null; // Modification pour utiliser DateTime
    
    #[ORM\ManyToOne(inversedBy: 'productReview')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $productReview = null;

    #[ORM\ManyToOne(inversedBy: 'reviewUser')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reviewUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProductReview(): ?Product
    {
        return $this->productReview;
    }

    public function setProductReview(?Product $productReview): static
    {
        $this->productReview = $productReview;

        return $this;
    }

    public function getReviewUser(): ?User
    {
        return $this->reviewUser;
    }

    public function setReviewUser(?User $reviewUser): static
    {
        $this->reviewUser = $reviewUser;

        return $this;
    }
}