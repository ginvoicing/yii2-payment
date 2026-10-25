<?php

namespace yii\payment;

interface ProviderInterface
{
    public function process(string $paymentReference): Response;

    public function validate(array $data): bool;
}
