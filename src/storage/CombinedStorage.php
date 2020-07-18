<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


use vsevolodryzhov\yii2Cart\AbstractProductConverter;
use vsevolodryzhov\yii2Cart\AbstractProductQuery;
use vsevolodryzhov\yii2Cart\CartItem;
use vsevolodryzhov\yii2Cart\StorageInterface;
use yii\db\Connection;
use yii\web\CookieCollection;
use yii\web\User;

class CombinedStorage implements StorageInterface
{
    private $storage;
    /**
     * @var User
     */
    private $user;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var CookieStorageSettings
     */
    private $cookieSettings;
    /**
     * @var CookieCollection
     */
    private $cookiesRequest;
    /**
     * @var CookieCollection
     */
    private $cookiesResponse;
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
    private $databaseStorageSettings;

    public function __construct(
        User $user,
        Connection $connection,
        CookieStorageSettings $cookieSettings,
        CookieCollection $cookiesRequest,
        CookieCollection $cookiesResponse,
        AbstractProductQuery $productQuery,
        AbstractProductConverter $converter,
        DatabaseStorageSettings $databaseStorageSettings
    )
    {
        $this->user = $user;
        $this->connection = $connection;
        $this->cookieSettings = $cookieSettings;
        $this->cookiesRequest = $cookiesRequest;
        $this->cookiesResponse = $cookiesResponse;
        $this->productQuery = $productQuery;
        $this->converter = $converter;
        $this->databaseStorageSettings = $databaseStorageSettings;
    }

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return $this->getStorage()->load();
    }

    /**
     * @inheritDoc
     */
    public function save($items): void
    {
        $this->getStorage()->save($items);
    }

    private function getStorage()
    {
        if ($this->storage === null) {
            $cookieStorage = new CookieStorage(
                $this->cookieSettings,
                $this->cookiesRequest,
                $this->cookiesResponse,
                $this->productQuery,
                $this->converter
            );
            if ($this->user->isGuest) {
                $this->storage = $cookieStorage;
            } else {
                $dbStorage = new DatabaseStorage($this->user->id, $this->connection, $this->productQuery, $this->converter, $this->databaseStorageSettings);
                if ($cookieItems = $cookieStorage->load()) {
                    $dbItems = $dbStorage->load();
                    $items = array_merge($dbItems, array_udiff($cookieItems, $dbItems, static function (CartItem $first, CartItem $second) {
                        return $first->getId() === $second->getId();
                    }));
                    $dbStorage->save($items);
                    $cookieStorage->save([]);
                }
                $this->storage = $dbStorage;
            }
        }
        return $this->storage;
    }
}