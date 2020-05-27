<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;

/**
 * Convert Product object to CartItem
 * Class AbstractProductConverter
 * @package vsevolodryzhov\yii2Cart
 */
abstract class AbstractProductConverter
{
    abstract public function convertProductToCartItem($product, int $quantity): CartItem;
}