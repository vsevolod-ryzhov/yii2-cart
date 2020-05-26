<?php

declare(strict_types=1);



namespace vsevolodryzhov\yii2Cart;


use PHPUnit_Framework_TestCase;
use vsevolodryzhov\yii2Cart\cost\BaseCost;

class FakeStorage implements StorageInterface
{
    private $items = [];

    public function load(): array
    {
        return $this->items;
    }

    public function save($items): void
    {
        $this->items = $items;
    }
}

class CartTest extends PHPUnit_Framework_TestCase
{
    private $cart;

    public function setUp()
    {
        $this->cart = new Cart(new FakeStorage(), new BaseCost());
        parent::setUp();
    }

    public function testExists()
    {
        $this->assertSame([], $this->cart->getItems());
    }

    public function testAdd()
    {
        $this->cart->add(1, 1000, 10);
        $items = $this->cart->getItems();
        $this->assertSame(1, $items[1]->getId());
        $this->assertSame(1000, $items[1]->getPrice());
        $this->assertSame(10, $items[1]->getCount());
        $this->cart->add(1, 1000, 2);
        $items = $this->cart->getItems();
        $this->assertSame(12, $items[1]->getCount());
    }

    public function testClear()
    {
        $this->cart->add(1, 1000, 10);
        $this->assertNotEmpty($this->cart->getItems());
        $this->cart->clear();
        $this->assertEmpty($this->cart->getItems());
    }

    public function testRemove()
    {
        $this->cart->add(1, 1000, 10);
        $this->assertNotEmpty($this->cart->getItems());
        $this->cart->removeItem(1);
        $this->assertEmpty($this->cart->getItems());
    }

    public function testCost()
    {
        $this->cart->add(1, 1000, 10);
        $this->cart->add(1, 1200, 2);
        $items = $this->cart->getItems();
        $this->assertSame(1200, $items[1]->getPrice());
        $this->assertSame(floatval(14400), $this->cart->getCost());
    }
}