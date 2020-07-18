<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


class DatabaseStorageSettings
{
    private $cartItemsTable = '{{%cart_items}}';
    private $userIdField = 'user_id';
    private $productIdField = 'product_id';
    private $quantityField = 'quantity';
    private $productTableIdField = 'id';

    public function __construct(
        $cartItemsTable = null,
        $userIdField = null,
        $productIdField = null,
        $quantityField = null,
        $productTableIdField = null
    )
    {
        if ($cartItemsTable) {
            $this->cartItemsTable = $cartItemsTable;
        }
        if ($userIdField) {
            $this->userIdField = $userIdField;
        }
        if ($productIdField) {
            $this->productIdField = $productIdField;
        }
        if ($quantityField) {
            $this->quantityField = $quantityField;
        }
        if ($productTableIdField) {
            $this->productTableIdField = $productTableIdField;
        }
    }

    /**
     * @return string
     */
    public function getCartItemsTable(): string
    {
        return $this->cartItemsTable;
    }

    /**
     * @param string $cartItemsTable
     */
    public function setCartItemsTable(string $cartItemsTable): void
    {
        $this->cartItemsTable = $cartItemsTable;
    }

    /**
     * @return string
     */
    public function getUserIdField(): string
    {
        return $this->userIdField;
    }

    /**
     * @param string $userIdField
     */
    public function setUserIdField(string $userIdField): void
    {
        $this->userIdField = $userIdField;
    }

    /**
     * @return string
     */
    public function getProductIdField(): string
    {
        return $this->productIdField;
    }

    /**
     * @param string $productIdField
     */
    public function setProductIdField(string $productIdField): void
    {
        $this->productIdField = $productIdField;
    }

    /**
     * @return string
     */
    public function getQuantityField(): string
    {
        return $this->quantityField;
    }

    /**
     * @param string $quantityField
     */
    public function setQuantityField(string $quantityField): void
    {
        $this->quantityField = $quantityField;
    }

    /**
     * @return string
     */
    public function getProductTableIdField(): string
    {
        return $this->productTableIdField;
    }

    /**
     * @param string $productTableIdField
     */
    public function setProductTableIdField(string $productTableIdField): void
    {
        $this->productTableIdField = $productTableIdField;
    }


}