<?php

namespace yii\payment;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\payment\exceptions\BadRequest;
use yii\payment\exceptions\InvalidProviderConfig;
use yii\payment\exceptions\ProviderNotFound;
use yii\payment\logging\Logger;
use yii\payment\logging\LoggerInterface;

class Gateway extends Component
{
    public array $providers = [];

    /**
     * @var false|array If you want to enable logging while success and failure of the payment. logging = ['connection' => 'db']
     */
    public false|array $logging = false;
    private LoggerInterface|null $_logger = null;


    public function init()
    {
        parent::init();

        if (count($this->providers) === 0) {
            throw new InvalidConfigException('Property "providers" is mandatory for payment component.');
        }

        if ($this->logging !== false && $this->_logger === null) {
            if (empty($this->logging['connection'])) {
                throw new InvalidConfigException('For logging, you must have to provide db connection.');
            }
            if (empty($this->logging['class'])) {
                $this->logging['class'] = Logger::class;
            }
            $this->_logger = \Yii::createObject($this->logging);
        }
    }

    public function process(string $gatewayName, string $paymentReference, array $apiCredentials = []): Response
    {
        if (!isset($this->providers[$gatewayName])) {
            throw new ProviderNotFound("Payment gateway \"$gatewayName\" does not exist in providers settings.");
        }

        if (!isset($this->providers[$gatewayName]['apiKey'], $this->providers[$gatewayName]['apiSecret'])) {
            throw new InvalidProviderConfig("apiKey and apiSecret are required parameter of \"$gatewayName\" provider.");
        }

        /** @var ProviderInterface $selectedProvider */
        $selectedProvider = \Yii::createObject($this->providers[$gatewayName]);

        if (count($apiCredentials) && !(isset($apiCredentials['apiKey']) && isset($apiCredentials['apiSecret']))) {
            throw new InvalidProviderConfig('Credentials apiKey & apiSecret are required.');
        }

        if (count($apiCredentials)) {
            $selectedProvider->apiKey = $apiCredentials['apiKey'];
            $selectedProvider->apiSecret = $apiCredentials['apiSecret'];
        }

        try {
            $response = $selectedProvider->process($paymentReference);
            $this->log($response);
            return $response;
        } catch (BadRequest $e) {
            $response = $e->getResponse();
            if ($response === null) {
                throw $e;
            }
            $this->log($response);
            throw new BadRequest((string) $response->getError(), $e->getCode(), $e, $response);
        }
    }

    public function getLogger(): LoggerInterface | false
    {
        if ($this->_logger instanceof LoggerInterface) {
            return $this->_logger;
        }

        return false;
    }

    private function log(Response $response): void
    {
        if ($this->logging === false || !$this->_logger instanceof LoggerInterface) {
            return;
        }

        $this->_logger->setRecord(
            [
                'payment_id' => $response->getPaymentId(),
                'phone' => $response->getContactPhone(),
                'email' => $response->getContactEmail(),
                'amount' => $response->getAmount(),
                'currency' => $response->getCurrency(),
                'status' => $response->getStatus(),
                'provider' => $response->getProvider(),
                'raw' => $response->getEncodedRaw()
            ]
        );
    }
}
