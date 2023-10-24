<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Security\Voter\LineOrderVoter;
use App\Repository\LineOrderRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LineOrderRepository::class)]
#[ApiResource(
    operations : [
        new Get(
        ),
        new Post(
            securityPostDenormalize: "is_granted('" . LineOrderVoter::CREATE . "', object)",
        ),
        new Put(
            security: "is_granted('" . LineOrderVoter::EDIT . "', object)",
        ),
        new Patch(
            security: "is_granted('" . LineOrderVoter::EDIT . "', object)",
        ),
    ],
    normalizationContext: ['groups' => ['lineOrder:read']],
    denormalizationContext: ['groups' => ['lineOrder:write']],
)]
class LineOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Groups(['lineOrder:read', 'lineOrder:write'])]
    private $quantity;

    #[ORM\Column(type: 'float')]
    #[Groups(['lineOrder:read'])]
    private $subtotal;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lineOrders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['lineOrder:read', 'lineOrder:write'])]
    private $order_associated;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false,onDelete:"CASCADE")]
    #[Groups(['lineOrder:read', 'lineOrder:write'])]
    private $product;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getOrderAssociated(): ?Order
    {
        return $this->order_associated;
    }

    #[Groups(['lineOrder:read', 'lineOrder:write'])]
    public function setOrderAssociated(?Order $order_associated): self
    {
        $this->order_associated = $order_associated;
        $this->order_associated->setTotal($this->order_associated->getTotal()+$this->subtotal);

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        $price = $product->getPrice();
        $this->subtotal = $price * $this->quantity;

        return $this;
    }
    public function __toString(){
        return $this->quantity." ".$this->product->getName();
    }
}
