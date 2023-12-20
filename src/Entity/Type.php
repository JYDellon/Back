<?php

namespace App\Entity;


use App\Entity\Traits\SlugTrait;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeName = null;

    #[ORM\OneToMany(mappedBy: 'productType', targetEntity: Product::class)]
    private Collection $productType;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'types')]
    #[ORM\JoinColumn(onDelete:'CASCADE')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $types;

    public function __construct()
    {
        $this->productType = new ArrayCollection();
        $this->types = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): static
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductType(): Collection
    {
        return $this->productType;
    }

    public function addProductType(Product $productType): static
    {
        if (!$this->productType->contains($productType)) {
            $this->productType->add($productType);
            $productType->setProductType($this);
        }

        return $this;
    }

    public function removeProductType(Product $productType): static
    {
        if ($this->productType->removeElement($productType) && $productType->getProductType() === $this) {
            // set the owning side to null (unless already changed)
            $productType->setProductType(null);
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(self $type): static
    {
        if (!$this->types->contains($type)) {
            $this->types->add($type);
            $type->setParent($this);
        }

        return $this;
    }

    public function removeType(self $type): static
    {
        if ($this->types->removeElement($type)) {
            // set the owning side to null (unless already changed)
            if ($type->getParent() === $this) {
                $type->setParent(null);
            }
        }

        return $this;
    }
}