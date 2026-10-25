<?php

namespace data;

use yii\payment\provider\RazorPay;

/**
 * RazorPay provider that serves payments from memory instead of calling the Razorpay API.
 */
class RazorPayStub extends RazorPay
{
    /**
     * @var array<string, array> payment attributes keyed by payment id
     */
    public static array $payments = [];

    /**
     * @var array<int, array{string, string}> apiKey and apiSecret used for each fetch
     */
    public static array $credentials = [];

    protected function fetchPayment(string $paymentReference): array
    {
        self::$credentials[] = [$this->apiKey, $this->apiSecret];

        if (!isset(self::$payments[$paymentReference])) {
            throw new \RuntimeException("Unknown payment \"$paymentReference\".");
        }

        return self::$payments[$paymentReference];
    }
}
