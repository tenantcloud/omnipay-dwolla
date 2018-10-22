<?php

namespace Omnipay\Dwolla\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class Response
 * Dwolla response interface for OmniPay.
 * @package Omnipay\Dwolla\Message
 */
class Response extends AbstractResponse
{
    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->data['success'];
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return false;
    }

    /**
     * @return null|string
     */
    public function getTransactionReference(): ? string
    {
        return $this->data['referenceId'] ? basename($this->data['referenceId']) : null;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ? string
    {
        return $this->data['message'] ?? null;
    }

    public function getCode(): ? int
    {
        return $this->data['statusCode'] ?? null;
    }
}
