<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;


interface CalculatorInterface
{
    /**
     * @param CartItem[] $items
     * @return float
     */
    public function getCost($items): float;
}