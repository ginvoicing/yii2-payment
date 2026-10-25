<?php

namespace yii\payment\exceptions;

use yii\payment\Response;

class BadRequest extends \yii\base\Exception
{
    private ?Response $_response;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, ?Response $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_response = $response;
    }

    public function getName(): string
    {
        return 'BAD_PAYMENT_REQUEST';
    }

    /**
     * Response of the failed payment, if the provider returned one.
     */
    public function getResponse(): ?Response
    {
        return $this->_response;
    }
}
