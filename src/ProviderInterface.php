<?php

namespace yii\payment;

use yii\payment\exceptions\BalanceException;
use yii\payment\exceptions\RatePullException;

interface ProviderInterface
{
    public function process(string $paymentReference): Response;

    public function validate(array $data): bool;
}