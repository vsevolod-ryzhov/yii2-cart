<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


use vsevolodryzhov\yii2Cart\AbstractProductConverter;
use vsevolodryzhov\yii2Cart\AbstractProductQuery;
use vsevolodryzhov\yii2Cart\CartItem;
use vsevolodryzhov\yii2Cart\StorageInterface;
use yii\helpers\Json;
use yii\web\Cookie;
use yii\web\CookieCollection;

class CookieStorage implements StorageInterface
{
    private const PRODUCT_ID_KEY = 'p';
    private const PRODUCT_QUANTITY_KEY = 'q';

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $timeout;

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

    public $productTableIdField = 'id';

    public function __construct(
        string $key,
        int $timeout,
        CookieCollection $cookiesRequest,
        CookieCollection $cookiesResponse,
        AbstractProductQuery $productQuery,
        AbstractProductConverter $converter
    )
    {
        $this->key = $key;
        $this->timeout = $timeout;
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
        $items = [];
        if ($cookie = $this->cookiesRequest->get($this->key)) {
            foreach (Json::decode($cookie->value) as $cart_item) {
                $query = clone($this->productQuery);
                if (!isset($cart_item[self::PRODUCT_ID_KEY]) || !isset($cart_item[self::PRODUCT_QUANTITY_KEY])) {
                    continue;
                }

                if ($product = $query->andWhere([$this->productTableIdField => $cart_item[self::PRODUCT_ID_KEY]])->canBuy()->one()) {
                    $cartItem = $this->converter->convertProductToCartItem($product, intval($cart_item[self::PRODUCT_QUANTITY_KEY]));;
                    $items[$cartItem->getId()] = $cartItem;
                }
            }
        }
        return $items;
    }

    /**
     * @inheritDoc
     */
    public function save($items): void
    {
        $this->cookiesResponse->add(new Cookie([
            'name' => $this->key,
            'value' => Json::encode(array_map(function (CartItem $item) {
                return [
                    self::PRODUCT_ID_KEY => $item->getId(),
                    self::PRODUCT_QUANTITY_KEY => $item->getQuantity(),
                ];
            }, $items)),
            'expire' => time() + $this->timeout,
        ]));
    }
}