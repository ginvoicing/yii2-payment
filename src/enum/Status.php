<?php
namespace yii\payment\enum;

enum Status: string
{
    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';
}
