<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


class CookieStorageSettings
{
    /**
     * @var string
     */
    private $cookieKey;

    /**
     * @var int
     */
    private $cookieTimeout;

    /**
     * @var string
     */
    private $productTableIdField;

    public function __construct(string $cookieKey, int $cookieTimeout, string $productTableIdField = 'id')
    {
        $this->cookieKey = $cookieKey;
        $this->cookieTimeout = $cookieTimeout;
        $this->productTableIdField = $productTableIdField;
    }

    /**
     * @return string
     */
    public function getCookieKey(): string
    {
        return $this->cookieKey;
    }

    /**
     * @return int
     */
    public function getCookieTimeout(): int
    {
        return $this->cookieTimeout;
    }

    /**
     * @return string
     */
    public function getProductTableIdField(): string
    {
        return $this->productTableIdField;
    }
}