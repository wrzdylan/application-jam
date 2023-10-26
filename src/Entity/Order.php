<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Security\Voter\OrderVoter;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: "shop_order")]
#[ApiResource(
    operations : [
        new Get(
        ),
        new Post(
            securityPostDenormalize: "is_granted('" . OrderVoter::CREATE . "', object)",
        ),
        new Put(
            security: "is_granted('" . OrderVoter::EDIT . "', object)",
        ),
        new Patch(
            security: "is_granted('" . OrderVoter::EDIT . "', object)",
        ),
    ],
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']],
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read', 'order:write'])]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $datetime;

    #[ORM\Column(type: 'float')]
    #[Groups(['order:read'])]
    private $total;

    #[ORM\OneToMany(mappedBy: 'order_associated', targetEntity: LineOrder::class, cascade: ["persist"])]
    #[Groups(['order:read'])]
    private $lineOrders;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['order:read'])]
    private $status;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order:read', 'order:write'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->lineOrders = new ArrayCollection();
        $this->datetime = new \DateTime();
        $this->total = 0;
        $this->status = "pending";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }


    /**
     * @return Collection|LineOrder[]
     */
    public function getLineOrders(): Collection
    {
        return $this->lineOrders;
    }

    public function addLineOrder(LineOrder $lineOrder): self
    {
        if (!$this->lineOrders->contains($lineOrder)) {
            $this->lineOrders[] = $lineOrder;
            $lineOrder->setOrderAssociated($this);
        }

        return $this;
    }

    public function removeLineOrder(LineOrder $lineOrder): self
    {
        if ($this->lineOrders->removeElement($lineOrder)) {
            // set the owning side to null (unless already changed)
            if ($lineOrder->getOrderAssociated() === $this) {
                $lineOrder->setOrderAssociated(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}