<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;


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
        return $this->items;
    }

    public function add($id, $price, $count): void
    {
        $current = isset($this->items[$id]) ? $this->items[$id]->getCount() : 0;
        $this->items[$id] = new CartItem($id, $price, $current + $count);
        $this->saveItems();
    }

    public function removeItem($id): void
    {
        if (array_key_exists($id, $this->items)) {
            unset($this->items[$id]);
        }
        $this->saveItems();
    }

    public function getCost(): float
    {
        return $this->calculator->getCost($this->items);
    }

    private function loadItems(): void
    {
        $this->items = $this->storage->load();
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