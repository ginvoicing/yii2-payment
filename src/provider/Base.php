<?php

namespace yii\payment\provider;

use yii\base\UnknownPropertyException;

abstract class Base
{
    protected $_attributes;
    protected array $responseCodesMap;

    public function __construct(array $config = [])
    {
        $this->_attributes = $config;
    }

    public function __get(string $name): string
    {
        if (!isset($this->_attributes[$name])) {
            throw new UnknownPropertyException("\"{$name}\" is an invalid property.", 210419831);
        }
        return $this->_attributes[$name];
    }


    public function getResponseMessage(int $code): ?array
    {
        return $this->responseCodesMap[$code] ?? null;
    }

    public function getName(): string
    {
        return get_class($this);
    }
}