<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\cost;

use vsevolodryzhov\yii2Cart\CalculatorInterface;

class BaseCost implements CalculatorInterface
{
    /**
     * @inheritDoc
     */
    public function getCost($items): float
    {
        $cost = 0;
        foreach ($items as $item) {
            $cost += $item->getCost();
        }
        return $cost;
    }
}