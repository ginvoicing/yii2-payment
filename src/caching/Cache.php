<?php

namespace yii\payment\caching;

use yii\base\InvalidConfigException;
use yii\base\BaseObject;
use yii\di\Instance;
use yii\db\Connection;
use yii\payment\caching\models\CurrencyExchange;

final class Cache extends BaseObject implements CachingInterface
{
    public string $tableName = '{{%swiftcurrency_cache}}';

    /**
     * @var array|string|Connection
     */
    public $connection = null;
    private string $_model;

    public function init()
    {
        parent::init();
        $this->connection = Instance::ensure($this->connection);

        if ($this->connection instanceof Connection) {
            $this->_model = CurrencyExchange::class;
        } else {
            throw new InvalidConfigException("This connections doesn't support.");
        }
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function setRecord(array $tableAttributes): bool
    {
        $record = new $this->_model();
        foreach ($tableAttributes as $key => $value) {
            if ($record->hasAttribute(strtolower($key))) {
                $field = strtolower($key);
                $record->{$field} = $value;
            }
        }
        return $record->save();
    }

    /**
     * Get consumed quota from the database.
     *
     * @return array having quota and provider name
     */
    public function getUsedQuota(): array
    {
        $qProviders = $this->_model::find()
            ->select(['COUNT(*) AS quota', 'provider'])
            ->where('DATE_FORMAT(timeline,"%Y-%m") = DATE_FORMAT(NOW(),"%Y-%m")')
            ->groupBy(['provider', 'DATE_FORMAT(timeline,"%Y-%m")'])
            ->createCommand()->queryAll();

        $return = [];
        foreach ($qProviders as $qProvider) {
            $return[$qProvider['provider']] = $qProvider['quota'];
        }
        return $return;
    }
}