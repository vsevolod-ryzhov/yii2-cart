<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;


use DomainException;

class Cart
{
    private $items = [];
    private $storage;
    private $calculator;

    public function __construct(StorageInterface $storage, CalculatorInterface $calculator)
    {
        $this->storage = $storage;
        $this->calculator = $calculator;
        $this->loadItems();
    }

    public function getItems(): array
    {
        $this->loadItems();
        return $this->items;
    }

    public function addItem(ProductInterface $product, $count = 1): void
    {
        $this->loadItems();
        $id = $product->getId();
        $current_count = isset($this->items[$id]) ? $this->items[$id]->getQuantity() : 0;
        $this->items[$id] = new CartItem($product, $current_count + $count);
        $this->saveItems();
    }

    public function removeItem($id): void
    {
        $this->loadItems();
        if (array_key_exists($id, $this->items)) {
            unset($this->items[$id]);
            $this->saveItems();
            return;
        }
        throw new DomainException('Product not found in cart.');
    }

    public function getCost(): float
    {
        $this->loadItems();
        return $this->calculator->getCost($this->items);
    }

    private function loadItems(): void
    {
        if (empty($this->items)) {
            $this->items = $this->storage->load();
        }
    }

    private function saveItems(): void
    {
        $this->storage->save($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
        $this->saveItems();
    }

    public function hasItem(ProductInterface $product): bool
    {
        if (array_key_exists($product->getId(), $this->items)) {
            return true;
        }
        return false;
    }

    /**
     * Count of product in cart
     * @param ProductInterface $product
     * @return int
     */
    public function getItemQuantity(ProductInterface $product): int
    {
        $id = $product->getId();
        if (!array_key_exists($id, $this->items)) {
            return 0;
        }
        /* @var $item CartItem */
        $item = $this->items[$id];
        return $item->getQuantity();
    }

    /**
     * Cost of product in cart (price * quantity)
     * @param ProductInterface $product
     * @return int|null
     */
    public function getItemCost(ProductInterface $product): ?float
    {
        $id = $product->getId();
        if (!array_key_exists($id, $this->items)) {
            return null;
        }
        /* @var $item CartItem */
        $item = $this->items[$id];
        return $item->getCost();
    }

    /**
     * Update product in cart
     * @param ProductInterface $product
     * @param $count
     */
    public function updateItem(ProductInterface $product, $count): void
    {
        $this->loadItems();
        $id = $product->getId();
        $this->items[$id] = new CartItem($product, $count);
        $this->saveItems();
    }

    /**
     * Get product item by id
     * @param int $id
     * @return ProductInterface|null
     */
    public function getItem(int $id): ?ProductInterface
    {
        if (!array_key_exists($id, $this->items)) {
            return null;
        }

        /* @var $item CartItem */
        $item = $this->items[$id];
        return $item->getProduct();
    }
}