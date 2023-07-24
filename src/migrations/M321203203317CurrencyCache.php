<?php


namespace yii\payment\migrations;

use \yii\payment\caching\Cache;
use yii\payment\exceptions\BadCachingProvider;

class M321203203317PaymentResponse extends \yii\db\Migration
{
    public $tableName = '{{%payment_response}}';

    public function init()
    {
        /**
         * @var Cache $cacheProvider
         */
        $cacheProvider = \Yii::$app->payment->getCachingProvider();
        if ($cacheProvider === false) {
            throw new BadCachingProvider('Caching agent is required.');
        }
        $this->tableName = $cacheProvider->getTableName();
        $this->db = $cacheProvider->getConnection();
    }

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->notNull(),
            'timeline' => $this->timestamp()->defaultExpression('NOW()'),
            'provider' => $this->string(100),
            'raw' => $this->json(),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE NOW()'),
            'created_at' => $this->timestamp()->defaultExpression('NOW()')
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}