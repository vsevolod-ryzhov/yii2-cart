<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;


interface ProductInterface
{
    public function getId();
    public function getPrice();
}