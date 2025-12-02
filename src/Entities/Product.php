<?php
namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: \App\Repository\ProductRepository::class)]
#[ORM\Table(name: "Product")]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 50)]
    public string $id;

    #[ORM\Column(type: "string", length: 255)]
    public string $name;

    #[ORM\Column(type: "boolean")]
    public bool $in_stock;

    #[ORM\Column(type: "text", nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    public ?string $brand = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "products")]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    public ?Category $category = null;

    #[ORM\OneToMany(mappedBy: "product", targetEntity: Gallery::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    public Collection $gallery;

    #[ORM\OneToMany(mappedBy: "product", targetEntity: Price::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    public Collection $prices;

    #[ORM\OneToMany(mappedBy: "product", targetEntity: OrderItem::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    public Collection $orderItems;

    #[ORM\ManyToMany(targetEntity: Attribute::class, inversedBy: "products")]
    #[ORM\JoinTable(
        name: "ProductAttribute",
        joinColumns: [new ORM\JoinColumn(name: "product_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "attribute_id", referencedColumnName: "id", onDelete: "CASCADE")]
    )]
    public Collection $attributes;

    public function __construct()
    {
        $this->gallery = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }


    public function addAttribute(Attribute $attribute): void
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            if (property_exists($attribute, 'products') && !$attribute->products->contains($this)) {
                $attribute->products->add($this);
            }
        }
    }

    public function removeAttribute(Attribute $attribute): void
    {
        if ($this->attributes->removeElement($attribute) && property_exists($attribute, 'products')) {
            $attribute->products->removeElement($this);
        }
    }

    public function addGalleryItem(Gallery $gallery): void
    {
        if (!$this->gallery->contains($gallery)) {
            $this->gallery->add($gallery);
            $gallery->product = $this; // keep both sides in sync
        }
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->product = $this;
        }
    }

    public function removePrice(Price $price): void
    {
        if ($this->prices->removeElement($price)) {
            if ($price->product === $this) {
                $price->product = null;
            }
        }
    }


}
