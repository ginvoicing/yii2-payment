<?php
namespace yii\payment\exceptions;

class BadPaymentGateway extends \yii\base\Exception
{
    public function getName(): string
    {
        return 'BAD_PAYMENT_GATEWAY';
    }
}
