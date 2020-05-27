<?php

declare(strict_types=1);



namespace vsevolodryzhov\yii2Cart;


use DomainException;
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

class FakeProduct implements ProductInterface
{
    private $id;
    private $price;

    public function __construct($id = 1, $price = 1000)
    {
        $this->id = $id;
        $this->price = $price;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }
}

class CartTest extends PHPUnit_Framework_TestCase
{
    private $cart;
    private $product;

    public function setUp()
    {
        $this->cart = new Cart(new FakeStorage(), new BaseCost());
        $this->product = new FakeProduct();
        parent::setUp();
    }

    public function testExists()
    {
        $this->assertSame([], $this->cart->getItems());
    }

    public function testAdd()
    {
        $this->cart->add($this->product, 10);
        $items = $this->cart->getItems();
        $this->assertSame(1, $items[1]->getId());
        $this->assertSame(1000, $items[1]->getPrice());
        $this->assertSame(10, $items[1]->getQuantity());
        $this->cart->add($this->product, 2);
        $items = $this->cart->getItems();
        $this->assertSame(12, $items[1]->getQuantity());
    }

    public function testClear()
    {
        $this->cart->add($this->product, 10);
        $this->assertNotEmpty($this->cart->getItems());
        $this->cart->clear();
        $this->assertEmpty($this->cart->getItems());
    }

    public function testRemove()
    {
        $this->cart->add($this->product, 10);
        $this->assertNotEmpty($this->cart->getItems());
        $this->cart->removeItem(1);
        $this->assertEmpty($this->cart->getItems());
    }

    public function testCost()
    {
        $this->cart->add($this->product, 10);
        $this->cart->add(new FakeProduct(1, 1200), 2);
        $items = $this->cart->getItems();
        $this->assertSame(1200, $items[1]->getPrice());
        $this->assertSame(floatval(14400), $this->cart->getCost());
    }

    public function testRemoveNotExisted()
    {
        $this->cart->add($this->product, 1);
        $this->setExpectedException(DomainException::class, 'Product not found in cart.');
        $this->cart->removeItem(2);
    }
}