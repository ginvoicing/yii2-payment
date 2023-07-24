<?php

namespace yii\payment;

use yii\payment\enum\Status;
use \DateTime;
use yii\payment\provider\RazorPay;

final class Response
{

    private null|string $raw = null;
    private string $paymentId;
    private int $amount;
    private string $currency;
    private string $contactEmail;
    private string $contactPhone;
    private RazorPay $provider;

    // public function __construct()
    // {
    //     $this
    //         ->setStatus(Status::PENDING())
    //         ->setRaw('{"status":"ERROR", "message":"Connectivity problem."}');
    // }



    public function getRaw(): string
    {
        return $this->raw;
    }

    public function setRaw(string $raw): Response
    {
        $this->raw = $raw;
        return $this;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): Response
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): Response
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): Response
    {
        $this->currency = $currency;
        return $this;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): Response
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(string $contactPhone): Response
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }

    public function setProvider(RazorPay $provider): Response
    {
        $this->provider = $provider;
        return $this;
    }

    public function getProvider(): RazorPay
    {
        return $this->provider;
    }
}