<?php
namespace yii\payment\exceptions;

use yii\base\InvalidConfigException;

class InvalidProviderConfig extends InvalidConfigException
{
    public function getName(): string
    {
        return 'BAD_PAYMENT_REQUEST';
    }
}
