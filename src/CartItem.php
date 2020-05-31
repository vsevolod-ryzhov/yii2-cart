<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;

class CartItem
{
    /**
     * @var ProductInterface
     */
    private $product;

    private $quantity;

    public function __construct(ProductInterface $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getId()
    {
        return $this->product->getId();
    }

    public function getPrice()
    {
        return $this->product->getPrice();
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCost()
    {
        return $this->product->getPrice() * $this->quantity;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }
}