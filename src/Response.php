<?php

namespace yii\payment;

final class Response
{

    private null|array $_raw = null;
    private null|string $_paymentId;
    private int $_amount = 0;
    private null|string $_currency;
    private string $_contactEmail;
    private string $_contactPhone;
    private string $_provider;
    private string $_status;
    private string|null $_error = null;


    public function getRaw(): null|array
    {
        return $this->_raw;
    }

    public function getEncodedRaw(): string
    {
        return json_encode($this->_raw);
    }

    public function setRaw(array $raw): Response
    {
        $this->_raw = $raw;
        return $this;
    }

    public function getPaymentId(): null|string
    {
        return $this->_paymentId;
    }

    public function setPaymentId(string $paymentId): Response
    {
        $this->_paymentId = $paymentId;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->_amount;
    }

    public function setAmount(int $amount): Response
    {
        $this->_amount = $amount;
        return $this;
    }

    public function getCurrency(): null|string
    {
        return $this->_currency;
    }

    public function setCurrency(string $currency): Response
    {
        $this->_currency = $currency;
        return $this;
    }

    public function getContactEmail(): string
    {
        return $this->_contactEmail;
    }

    public function setContactEmail(string $contactEmail): Response
    {
        $this->_contactEmail = $contactEmail;
        return $this;
    }

    public function getContactPhone(): string
    {
        return $this->_contactPhone;
    }

    public function setContactPhone(string $contactPhone): Response
    {
        $this->_contactPhone = $contactPhone;
        return $this;
    }

    public function setProvider(string $provider): Response
    {
        $this->_provider = $provider;
        return $this;
    }

    public function getProvider(): string
    {
        return $this->_provider;
    }

    public function setStatus(string $status): Response
    {
        $this->_status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->_status;
    }

    public function setError(string $error): Response
    {
        $this->_error = $error;
        return $this;
    }

    public function getError(): string|null
    {
        return $this->_error;
    }
}