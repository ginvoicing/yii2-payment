<?php
namespace yii\payment\exceptions;

class BadRequest extends Exception
{
    public function getName(): string
    {
        return 'BAD_PAYMENT_REQUEST';
    }
}
