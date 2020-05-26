<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2Cart\storage;


use vsevolodryzhov\yii2Cart\StorageInterface;
use yii\web\Session;

class SessionStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var Session
     */
    private $session;

    public function __construct(string $key, Session $session)
    {
        $this->key = $key;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return $this->session->get($this->key, []);
    }

    /**
     * @inheritDoc
     */
    public function save($items): void
    {
        $this->session->set($this->key, $items);
    }
}