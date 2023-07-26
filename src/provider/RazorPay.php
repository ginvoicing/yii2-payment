<?php

namespace yii\payment\provider;

use yii\payment\enum\Status;
use yii\payment\exceptions\BadGateway;
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
        $responseObject = new Response();
        $razorPayApiCall = new Api($this->apiKey, $this->apiSecret);
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
            throw new BadGateway(serialize($responseObject));
        }
        return $responseObject;
    }

    public function validate(array $data): bool
    {
        return true;
    }
}