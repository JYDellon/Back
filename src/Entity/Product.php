<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\SlugTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;




#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use SlugTrait;
    use CreatedAtTrait;
    
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $taxe = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $detailedDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $mesurement = null;

    #[ORM\Column(length: 255)]
    private ?string $stock = null;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: Image::class)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'lineCartProduct', targetEntity: LineCart::class)]
    private Collection $lineCarts;

    #[ORM\OneToMany(mappedBy: 'productLine', targetEntity: LineProduct::class)]
    private Collection $productLine;

    #[ORM\ManyToOne(inversedBy: 'productType')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $productType = null;

    #[ORM\OneToMany(mappedBy: 'productReview', targetEntity: Review::class)]
    private Collection $productReview;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'productTag')]
    private Collection $productTag;

    #[ORM\OneToMany(mappedBy: 'productReturned', targetEntity: ReturnedProduct::class)]
    private Collection $productReturned;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->lineCarts = new ArrayCollection();
        $this->productLine = new ArrayCollection();
        $this->productReview = new ArrayCollection();
        $this->productTag = new ArrayCollection();
        $this->productReturned = new ArrayCollection();
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    // public function setPrice(float $price): static
    public function setPrice(string $price): static
    {
        // $this->price = sprintf("%.2f", $price);
        $this->price =  $price;

        return $this;
    }

    public function getTaxe(): ?string
    {
        return $this->taxe;
    }

    // public function setTaxe(float $taxe): static
    public function setTaxe(string $taxe): static
    {
        // $this->taxe = sprintf("%.2f", $taxe);
        $this->taxe = $taxe;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDetailedDescription(): ?string
    {
        return $this->detailedDescription;
    }

    public function setDetailedDescription(string $detailedDescription): static
    {
        $this->detailedDescription = $detailedDescription;

        return $this;
    }

    public function getMesurement(): ?string
    {
        return $this->mesurement;
    }

    public function setMesurement(string $mesurement): static
    {
        $this->mesurement = $mesurement;

        return $this;
    }

    public function getStock(): ?string
    {
        return $this->stock;
    }

    public function setStock(string $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setImage($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image) && $image->getImage() === $this) {
            // set the owning side to null (unless already changed)
            $image->setImage(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, LineCart>
     */
    public function getLineCarts(): Collection
    {
        return $this->lineCarts;
    }

    public function addLineCart(LineCart $lineCart): static
    {
        if (!$this->lineCarts->contains($lineCart)) {
            $this->lineCarts->add($lineCart);
            $lineCart->setLineCartProduct($this);
        }

        return $this;
    }

    public function removeLineCart(LineCart $lineCart): static
    {
        if ($this->lineCarts->removeElement($lineCart) && $lineCart->getLineCartProduct() === $this) {
            // set the owning side to null (unless already changed)
            $lineCart->setLineCartProduct(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, LineProduct>
     */
    public function getProductLine(): Collection
    {
        return $this->productLine;
    }

    public function addProductLine(LineProduct $productLine): static
    {
        if (!$this->productLine->contains($productLine)) {
            $this->productLine->add($productLine);
            $productLine->setProductLine($this);
        }

        return $this;
    }

    public function removeProductLine(LineProduct $productLine): static
    {
        if ($this->productLine->removeElement($productLine) && $productLine->getProductLine() === $this) {
            // set the owning side to null (unless already changed)
            $productLine->setProductLine(null);
        }

        return $this;
    }

    /**
     * Get the value of productType
     */
    public function getProductType(): ?Type
    {
        return $this->productType;
    }

    /**
     * Set the value of productType
     */
    public function setProductType(?Type $productType): static
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getProductReview(): Collection
    {
        return $this->productReview;
    }

    public function addProductReview(Review $productReview): static
    {
        if (!$this->productReview->contains($productReview)) {
            $this->productReview->add($productReview);
            $productReview->setProductReview($this);
        }

        return $this;
    }

    public function removeProductReview(Review $productReview): static
    {
        if ($this->productReview->removeElement($productReview) && $productReview->getProductReview() === $this) {
            // set the owning side to null (unless already changed)
            $productReview->setProductReview(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getProductTag(): Collection
    {
        return $this->productTag;
    }

    public function addProductTag(Tag $productTag): static
    {
        if (!$this->productTag->contains($productTag)) {
            $this->productTag->add($productTag);
        }

        return $this;
    }

    public function removeProductTag(Tag $productTag): static
    {
        $this->productTag->removeElement($productTag);

        return $this;
    }

    /**
     * @return Collection<int, ReturnedProduct>
     */
    public function getProductReturned(): Collection
    {
        return $this->productReturned;
    }

    public function addProductReturned(ReturnedProduct $productReturned): static
    {
        if (!$this->productReturned->contains($productReturned)) {
            $this->productReturned->add($productReturned);
            $productReturned->setProductReturned($this);
        }

        return $this;
    }

    public function removeProductReturned(ReturnedProduct $productReturned): static
    {
        if ($this->productReturned->removeElement($productReturned)
         && $productReturned->getProductReturned() === $this) {
            // set the owning side to null (unless already changed)
            $productReturned->setProductReturned(null);
        }

        return $this;
    }

   }