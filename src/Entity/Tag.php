<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tagName = null;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'productTag')]
    private Collection $productTag;

    public function __construct()
    {
        $this->productTag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTagName(): ?string
    {
        return $this->tagName;
    }

    public function setTagName(string $tagName): static
    {
        $this->tagName = $tagName;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductTag(): Collection
    {
        return $this->productTag;
    }

    public function addProductTag(Product $productTag): static
    {
        if (!$this->productTag->contains($productTag)) {
            $this->productTag->add($productTag);
            $productTag->addProductTag($this);
        }

        return $this;
    }

    public function removeProductTag(Product $productTag): static
    {
        if ($this->productTag->removeElement($productTag)) {
            $productTag->removeProductTag($this);
        }

        return $this;
    }
}
