<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart;


use yii\db\ActiveQuery;

abstract class AbstractProductQuery extends ActiveQuery
{
    /**
     * Ensure that product can be purchased
     * Collect all functions like active(), available() & etc. here
     * @return ActiveQuery
     */
    public function canBuy(): ActiveQuery
    {
        return $this;
    }
}