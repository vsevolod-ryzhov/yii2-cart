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
     * @var string
     */
    private $cookieKey;
    /**
     * @var int
     */
    private $cookieTimeout;
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

    public function __construct(
        User $user,
        Connection $connection,
        string $cookieKey,
        int $cookieTimeout,
        CookieCollection $cookiesRequest,
        CookieCollection $cookiesResponse,
        AbstractProductQuery $productQuery,
        AbstractProductConverter $converter
    )
    {
        $this->user = $user;
        $this->connection = $connection;
        $this->cookieKey = $cookieKey;
        $this->cookieTimeout = $cookieTimeout;
        $this->cookiesRequest = $cookiesRequest;
        $this->cookiesResponse = $cookiesResponse;
        $this->productQuery = $productQuery;
        $this->converter = $converter;
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
            $cookieStorage = new CookieStorage($this->cookieKey, $this->cookieTimeout, $this->cookiesRequest, $this->cookiesResponse, $this->productQuery, $this->converter);
            if ($this->user->isGuest) {
                $this->storage = $cookieStorage;
            } else {
                $dbStorage = new DatabaseStorage($this->user->id, $this->connection, $this->productQuery, $this->converter);
                if ($cookieItems = $cookieStorage->load()) {
                    $dbItems = $dbStorage->load();
                    $items = array_merge($dbItems, array_udiff($cookieItems, $dbItems, function (CartItem $first, CartItem $second) {
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