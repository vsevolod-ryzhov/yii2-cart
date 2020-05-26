<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;


interface StorageInterface
{
    /**
     * @return CartItem[]
     */
    public function load(): array;

    /**
     * @param CartItem[] $items
     */
    public function save($items): void;
}