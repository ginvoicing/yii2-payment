<?php

namespace data;

use yii\db\ActiveRecord;

class SqlModel extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->payment->getLogger()->getConnection();
    }

    public static function tableName()
    {
        return \Yii::$app->payment->getLogger()->getTableName();
    }

    public function rules()
    {
        return [
            [['payment_id', 'currency', 'status', 'raw', 'provider'], 'required'],
            [['payment_id'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 25],
            [['amount'], 'number'],
            [['provider'], 'string', 'max' => 50]
        ];
    }
}
