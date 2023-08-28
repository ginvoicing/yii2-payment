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

    public function process(string $paymentReference, array $apiCredentials = []): Response
    {
        $responseObject = new Response();
        $apiKey = $apiCredentials['api_key'] ?? $this->apiKey;
        $apiSecret = $apiCredentials['api_secret'] ?? $this->apiSecret;
        $razorPayApiCall = new Api($apiKey, $apiSecret);
        $razorPayResponse = $razorPayApiCall->payment->fetch($paymentReference);

        $responseObject->setRaw($razorPayResponse->toArray());
        $responseObject->setPaymentId($razorPayResponse->id);
        $responseObject->setAmount($razorPayResponse->amount);
        $responseObject->setCurrency($razorPayResponse->currency);
        $responseObject->setContactEmail($razorPayResponse->email);
        $responseObject->setContactPhone($razorPayResponse->contact);
        $responseObject->setStatus($razorPayResponse->status === 'authorized' ? Status::SUCCESS->value : Status::FAILED->value);
        if (strtoupper($razorPayResponse->status) === Status::FAILED->value) {
            $responseObject->setError($razorPayResponse->error_description);
            throw new BadRequest(serialize($responseObject));
        }
        return $responseObject;
    }

    public function validate(array $data): bool
    {
        return true;
    }
}
