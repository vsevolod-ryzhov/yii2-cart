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

    public function add(ProductInterface $product, $count = 1): void
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
}