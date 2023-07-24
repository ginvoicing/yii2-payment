<?php
namespace yii\payment\caching;

interface CachingInterface
{
    /**
     * Add new record to currency caching table
     *
     * @param array $tableAttributes
     * @return bool
     */
    public function setRecord(array $tableAttributes): bool;
}