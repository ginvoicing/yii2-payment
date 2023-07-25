<?php

namespace yii\payment\logging;

use yii\base\InvalidConfigException;
use yii\base\BaseObject;
use yii\di\Instance;
use yii\db\Connection as SqlConnection;
use yii\payment\logging\models\Sql;

class Logger extends BaseObject implements LoggerInterface
{
    /**
     * @var string
     */
    public $tableName = '{{%payment_logger}}';

    /**
     * @var array|string|\yii\db\Connection
     */
    public $connection = null;

    /**
     * Log table model
     * @var Sql
     */
    private $_model;

    public function init()
    {
        $this->connection = Instance::ensure($this->connection);

        if ($this->connection instanceof SqlConnection) {
            $this->_model = Sql::class;
        } else {
            throw new InvalidConfigException("This connections doesn't support.");
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function setRecord($data): bool
    {
        $record = new $this->_model();
        foreach ($data as $key => $value) {
            if ($record->hasAttribute($key)) {
                $record->$key = $value;
            }
        }

        return $record->save();
    }

    /**
     * @inheritdoc
     */
    public function updateRecordByPaymentId(string $paymentId, array $data): bool
    {
        if (!empty($paymentId)) {
            $record = new $this->_model();
            $record = $record->findOne(['response_id' => $paymentId]);
            if ($record) {
                foreach ($data as $key => $value) {
                    if ($record->hasAttribute($key)) {
                        $record->$key = $value;
                    }
                }

                return $record->save();
            }
        }

        return false;
    }

}
