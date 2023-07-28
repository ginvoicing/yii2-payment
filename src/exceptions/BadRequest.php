<?php
namespace yii\payment\exceptions;

class BadRequest extends \yii\base\Exception
{
    public function getName(): string
    {
        return 'BAD_PAYMENT_REQUEST';
    }
}
