<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


use vsevolodryzhov\yii2Cart\AbstractProductConverter;
use vsevolodryzhov\yii2Cart\AbstractProductQuery;
use vsevolodryzhov\yii2Cart\CartItem;
use vsevolodryzhov\yii2Cart\StorageInterface;
use yii\db\Connection;
use yii\db\Query;

class DatabaseStorage implements StorageInterface
{
    private $userId;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var AbstractProductQuery
     */
    private $productQuery;

    /**
     * @var AbstractProductConverter
     */
    private $converter;

    public $cartItemsTable = '{{%cart_items}}';
    public $userIdField = 'user_id';
    public $productIdField = 'product_id';
    public $quantityField = 'quantity';
    public $productTableIdField = 'id';

    public function __construct($userId, Connection $connection, AbstractProductQuery $productQuery, AbstractProductConverter $converter)
    {
        $this->userId = $userId;
        $this->connection = $connection;
        $this->productQuery = $productQuery;
        $this->converter = $converter;
    }

    public function load(): array
    {
        $rows = (new Query())
            ->select('*')
            ->from($this->cartItemsTable)
            ->where([$this->userIdField => $this->userId])
            ->orderBy([$this->productIdField => SORT_ASC])
            ->all($this->connection);

        $items = [];
        foreach ($rows as $row) {
            if ($product = $this->productQuery->andWhere([$this->productTableIdField => $row[$this->productIdField]])->canBuy()->one()) {
                $cartItem = $this->converter->convertProductToCartItem($product, intval($row[$this->quantityField]));;
                $items[$cartItem->getId()] = $cartItem;
            }
        }

        return $items;
    }

    public function save($items): void
    {
        $this->connection->createCommand()->delete($this->cartItemsTable, [
            $this->userIdField => $this->userId,
        ])->execute();

        $this->connection->createCommand()->batchInsert(
            $this->cartItemsTable,
            [
                $this->userIdField,
                $this->productIdField,
                $this->quantityField
            ],
            array_map(function (CartItem $item) {
                return [
                    $this->userIdField => $this->userId,
                    $this->productIdField => $item->getId(),
                    $this->quantityField => $item->getQuantity(),
                ];
            }, $items)
        )->execute();
    }
}