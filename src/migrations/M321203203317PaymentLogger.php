<?php
namespace yii\payment\migrations;

class M321203203317PaymentLogger extends \yii\db\Migration
{
    public $tableName = '{{%payment_logger}}';

    public function init()
    {
        /**
         * @var \yii\payment\logging\Logger $logger
         */
        $logger = \Yii::$app->payment->getLogger();
        if ($logger === false) {
            throw new \Exception('Logger must be set');
        }
        $this->tableName = $logger->getTableName();
        $this->db = $logger->getConnection();
    }

    public function safeUp(): bool
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->notNull(),
            'payment_id' => $this->string(100)->notNull(),
            'phone' => $this->string(25)->null(),
            'email' => $this->string(50)->null(),
            'amount' => $this->integer(20)->defaultValue(0),
            'currency' => $this->string(3)->notNull(),
            'status' => $this->string(20)->notNull(),
            'raw' => $this->text()->notNull(),
            'provider' => $this->string(100)->notNull(),
            'updated_at' => $this->timestamp(),
            'created_at' => $this->timestamp()
        ], $tableOptions);
        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTable($this->tableName);
        return true;
    }
}
