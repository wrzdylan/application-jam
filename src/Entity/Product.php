<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Security\Voter\ProductVoter;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: "shop_product")]
#[ApiResource(
    operations : [
        new GetCollection(
            security: "is_granted('" . ProductVoter::VIEW . "', object)",
        ),
        new Get(
            security: "is_granted('" . ProductVoter::VIEW . "', object)",
        ),
        new Post(
            securityPostDenormalize: "is_granted('" . ProductVoter::CREATE . "', object)",
        ),
        new Put(
            security: "is_granted('" . ProductVoter::EDIT . "', object)",
        ),
        new Delete(
            security: "is_granted('" . ProductVoter::DELETE . "', object)",
        ),
        new Patch(
            security: "is_granted('" . ProductVoter::EDIT . "', object)",
        ),
    ],
    normalizationContext: ['groups' => ['product:read']],
    denormalizationContext: ['groups' => ['product:write']],
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['product:read', 'product:write'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['product:read', 'product:write'])]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['product:read', 'product:write'])]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['product:read', 'product:write'])]
    private $image;

    #[ORM\Column(type: 'integer')]
    #[Groups(['product:read', 'product:write'])]
    private $price;

    private int $quantity=0;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups(['product:read', 'product:write'])]
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFullName(): ?string
    {
        return $this->name." - ".$this->price;
    }
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }



    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);


        return $this;
    }

    public function __toString(){
        return $this->name;
    }
}