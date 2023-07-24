<?php
namespace yii\payment\exceptions;

class RatePullException extends Exception
{
    public function getName(): string
    {
        return 'Rate Pull Exception';
    }
}