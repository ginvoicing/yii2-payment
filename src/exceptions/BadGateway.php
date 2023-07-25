<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 07:28
 */

namespace yii\payment\exceptions;

class BadGateway extends Exception
{
    public function getName(): string
    {
        return 'Bad gateway';
    }
}