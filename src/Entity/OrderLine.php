<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=OrderLineRepository::class)
 */
class OrderLine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * 
     * @Assert\NotNull(
     *      message="Please provide a valid Quantity"
     * )
     * @Assert\GreaterThanOrEqual(
     *     value="1",
     *     message="Please provide quantity >= 1"
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} must be a valid {{ type }}."
     * )
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orderLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderLines", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $cart;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCart(): ?Order
    {
        return $this->cart;
    }

    public function setCart(?Order $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Checks if the quantity has been exceeded.
     *
     * @return bool true if the quantity has been exceed. false if not
     *
     * @Assert\IsTrue(message="The quantity allowed has been  exceeded.")
     */
    public function isQuantityExceeded(): bool
    {
        return !($this->getQuantity() > $this->getProduct()->getQuantity());
    }

    /**
     * @required
     *
     * @param TranslatorInterface $translator
    */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @Assert\Callback()
     *
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function exceeded(ExecutionContextInterface $context, $payload)
    {
        // $message = $this->translator->trans("The quantity has exceeded the max in the store");
        $message = "The quantity has exceeded the max in the store";
        if ($this->getQuantity() > $this->getProduct()->getQuantity()) {
            $context->buildViolation($message)
                ->atPath('quantity')
                ->addViolation();
        }
    }

    /**
     * Calculate total of one orderline
     *
     * @return float
     */
    public function totalOrderLine():float
    {
        return $this->getQuantity()*$this->getProduct()->getUnitPrice();    
    }
}
