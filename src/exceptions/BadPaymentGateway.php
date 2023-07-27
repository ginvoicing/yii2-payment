<?php
namespace yii\payment\exceptions;

class BadPaymentGateway extends Exception
{
    public function getName(): string
    {
        return 'BAD_PAYMENT_GATEWAY';
    }
}
