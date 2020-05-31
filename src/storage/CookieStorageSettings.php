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

    public function __construct(string $cookieKey, int $cookieTimeout)
    {
        $this->cookieKey = $cookieKey;
        $this->cookieTimeout = $cookieTimeout;
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
}