<?php
namespace yii\payment\logging;

interface LoggerInterface
{
    /**
     * Add new record to log
     *
     * @param array $data
     * @return bool
     */
    public function setRecord(array $data): bool;

    /**
     * Update log record by payment id
     *
     * @param string $response_id
     * @param array $data
     * @return bool
     */
    public function updateRecordByPaymentId(string $paymentId, array $data): bool;
}