<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


use Exception;
use vsevolodryzhov\yii2Cart\AbstractProductConverter;
use vsevolodryzhov\yii2Cart\AbstractProductQuery;
use vsevolodryzhov\yii2Cart\CartItem;
use vsevolodryzhov\yii2Cart\StorageException;
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

    /**
     * @var DatabaseStorageSettings
     */
    private $settings;

    public function __construct(
        $userId,
        Connection $connection,
        AbstractProductQuery $productQuery,
        AbstractProductConverter $converter,
        DatabaseStorageSettings $settings
    )
    {
        $this->userId = $userId;
        $this->connection = $connection;
        $this->productQuery = $productQuery;
        $this->converter = $converter;
        $this->settings = $settings;
    }

    public function load(): array
    {
        $rows = (new Query())
            ->select('*')
            ->from($this->settings->getCartItemsTable())
            ->where([$this->settings->getUserIdField() => $this->userId])
            ->orderBy([$this->settings->getProductIdField() => SORT_ASC])
            ->all($this->connection);

        $items = [];
        foreach ($rows as $row) {
            $query = clone($this->productQuery);
            if ($product = $query->andWhere([$this->settings->getProductTableIdField() => $row[$this->settings->getProductIdField()]])->canBuy()->one()) {
                $cartItem = $this->converter->convertProductToCartItem($product, (int) $row[$this->settings->getQuantityField()]);
                $items[$cartItem->getId()] = $cartItem;
            }
        }

        return $items;
    }

    public function save($items): void
    {
        try {
            $this->connection->createCommand()->delete($this->settings->getCartItemsTable(), [
                $this->settings->getUserIdField() => $this->userId,
            ])->execute();
        } catch (Exception $e) {
            throw new StorageException('Database error: ' . $e->getMessage());
        }

        try {
            $this->connection->createCommand()->batchInsert(
                $this->settings->getCartItemsTable(),
                [
                    $this->settings->getUserIdField(),
                    $this->settings->getProductIdField(),
                    $this->settings->getQuantityField()
                ],
                array_map(function (CartItem $item) {
                    return [
                        $this->settings->getUserIdField() => $this->userId,
                        $this->settings->getProductIdField() => $item->getId(),
                        $this->settings->getQuantityField() => $item->getQuantity(),
                    ];
                }, $items)
            )->execute();
        } catch (Exception $e) {
            throw new StorageException('Database error: ' . $e->getMessage());
        }
    }
}