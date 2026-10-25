<?php

namespace yii\payment\provider;

use yii\payment\enum\Status;
use yii\payment\exceptions\BadRequest;
use yii\payment\ProviderInterface;
use yii\payment\Response;
use Razorpay\Api\Api;

class RazorPay extends Base implements ProviderInterface
{
    public string $apiKey;
    public string $apiSecret;
    protected array $responseCodesMap = [

    ];

    public function process(string $paymentReference): Response
    {
        $payment = $this->fetchPayment($paymentReference);

        $responseObject = new Response();
        $responseObject->setRaw($payment);
        $responseObject->setPaymentId((string) ($payment['id'] ?? $paymentReference));
        $responseObject->setAmount((float) ($payment['amount'] ?? 0));
        $responseObject->setCurrency($payment['currency'] ?? null);
        $responseObject->setContactEmail(null);
        $responseObject->setContactPhone(isset($payment['contact']) ? (string) $payment['contact'] : null);
        $responseObject->setProvider(static::class);
        $status = (string) ($payment['status'] ?? '');
        $responseObject->setStatus($status === 'authorized' ? Status::SUCCESS->value : Status::FAILED->value);
        if (strtoupper($status) === Status::FAILED->value) {
            $error = (string) ($payment['error_description'] ?? 'Payment failed.');
            $responseObject->setError($error);
            throw new BadRequest($error, 0, null, $responseObject);
        }
        return $responseObject;
    }

    public function validate(array $data): bool
    {
        return true;
    }

    /**
     * Fetches the payment from the Razorpay API.
     *
     * @return array payment attributes as returned by Razorpay
     */
    protected function fetchPayment(string $paymentReference): array
    {
        $razorPayApiCall = new Api($this->apiKey, $this->apiSecret);

        return $razorPayApiCall->payment->fetch($paymentReference)->toArray();
    }
}
