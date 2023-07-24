<?php

namespace yii\payment\caching\models;

use yii\db\ActiveRecord;

class PaymentResponse extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->payment->getCachingProvider()->getConnection();
    }

    public static function tableName(): string
    {
        return \Yii::$app->payment->getCachingProvider()->getTableName();
    }

    public function rules()
    {
        return [
            [['provider'], 'string', 'max' => 100]
        ];
    }
}