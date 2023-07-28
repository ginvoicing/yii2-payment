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
     * @var bool If you want to enable logging while success and failure of the payment. logging = ['connection' => 'db']
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
            if (!isset($this->logging['connection']) || empty($this->logging['connection'])
                || (is_array($this->logging['connection']) && count((array) $this->logging['connection']) === 0)
            ) {
                throw new InvalidConfigException('For logging, you must have to provide db connection.');
            }
            if (!isset($this->logging['class']) || empty($this->logging['class'])) {
                $this->logging['class'] = Logger::class;
            }
            $this->_logger = \Yii::createObject($this->logging);
        }
    }

    public function process(string $gatewayName, string $paymentReference): Response
    {
        /**
 * ProviderInterface $selectedProvider
*/

        if (!isset($this->providers[$gatewayName])) {
            throw new ProviderNotFound("Payment gatway \"$gatewayName\" does not exist in providers settings.");
        }

        if (!isset($this->providers[$gatewayName]['apiKey'])
            && !isset($this->providers[$gatewayName]['apiSecret'])
        ) {
            throw new InvalidProviderConfig("apiKey and apiSecret are required parameter of \"$gatewayName\" provider.");
        }

        $selectedProvider = \Yii::createObject($this->providers[$gatewayName]);

        try {
            $response = $selectedProvider->process($paymentReference);
            if ($this->logging !== false && $this->_logger instanceof LoggerInterface) {
                $this->_logger->setRecord(
                    [
                    'payment_id' => $response->getPaymentId(),
                    'phone' => $response->getContactPhone(),
                    'email' => $response->getContactEmail(),
                    'amount' => $response->getAmount(),
                    'currency' => $response->getCurrency(),
                    'status' => $response->getStatus(),
                    'provider' => get_class($selectedProvider),
                    'raw' => $response->getEncodedRaw()
                    ]
                );
            }
            return $response;
        } catch (BadRequest $e) {
            if ($this->logging !== false && $this->_logger instanceof LoggerInterface) {
                $response = unserialize($e->getMessage());
                $this->_logger->setRecord(
                    [
                    'payment_id' => $response->getPaymentId(),
                    'phone' => $response->getContactPhone(),
                    'email' => $response->getContactEmail(),
                    'amount' => $response->getAmount(),
                    'currency' => $response->getCurrency(),
                    'status' => $response->getStatus(),
                    'provider' => get_class($selectedProvider),
                    'raw' => $response->getEncodedRaw()
                    ]
                );
            }
            throw new BadRequest($response->getError());
        }
    }

    public function getLogger()
    {
        if ($this->_logger instanceof LoggerInterface) {
            return $this->_logger;
        }

        return false;
    }
}
