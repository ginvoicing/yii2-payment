<?php

namespace data;

use yii\db\ActiveRecord;

class SqlModel extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->swiftsmser->getLogger()->getConnection();
    }

    public static function tableName()
    {
        return \Yii::$app->swiftsmser->getLogger()->getTableName();
    }

    public function rules()
    {
        return [
            [['timeline', 'base_currency', 'exchange_rates', 'provider'], 'required'],
            [['provider'], 'string', 'max' => 100]
        ];
    }
}
