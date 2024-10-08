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
        $responseObject = new Response();
        $razorPayApiCall = new Api($this->apiKey, $this->apiSecret);
        $razorPayResponse = $razorPayApiCall->payment->fetch($paymentReference);

        $responseObject->setRaw($razorPayResponse->toArray());
        $responseObject->setPaymentId($razorPayResponse->id);
        $responseObject->setAmount($razorPayResponse->amount);
        $responseObject->setCurrency($razorPayResponse->currency);
        $responseObject->setContactEmail(null);
        $responseObject->setContactPhone($razorPayResponse->contact);
        $responseObject->setProvider(get_class($this));
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
