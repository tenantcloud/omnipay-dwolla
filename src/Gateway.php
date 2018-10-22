<?php

namespace Omnipay\Dwolla;

use Omnipay\Common\AbstractGateway;

/**
 * Dwolla Gateway
 * @link https://developers.dwolla.com/
 */
class Gateway extends AbstractGateway
{
    use GetterSetterTrait;

    public function getName()
    {
        return 'Dwolla';
    }

    /**
     * Retrieves default parameters for the OmniPay
     * standardized field names
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'appToken' => null,
            'testMode' => null,
            'amount' => null,
            'sourceId' => null,
            'sourceType' => 'funding-sources',
            'destinationId' => null,
            'destinationType' => 'funding-sources',
            'metadata' => null,
            'fees' => null,
            'correlationId' => null,
            'achDetails' => null,
            'clearing' => 'standard',
            'massPayments' => null,
        ];
    }

    public function getAppToken()
    {
        return $this->getParameter('appToken');
    }

    public function setAppToken($value)
    {
        return $this->setParameter('appToken', $value);
    }

    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }

    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    public function getSourceId()
    {
        return $this->getParameter('sourceId');
    }

    public function setSourceId($value)
    {
        return $this->setParameter('sourceId', $value);
    }

    public function getSourceType()
    {
        return $this->getParameter('sourceType');
    }

    public function setSourceType($value)
    {
        return $this->setParameter('sourceType', $value);
    }

    public function getDestinationId()
    {
        return $this->getParameter('destinationId');
    }

    public function setDestinationId($value)
    {
        return $this->setParameter('destinationId', $value);
    }

    public function getDestinationType()
    {
        return $this->getParameter('destinationType');
    }

    public function setDestinationType($value)
    {
        return $this->setParameter('destinationType', $value);
    }

    public function getMetadata()
    {
        return $this->getParameter('metadata');
    }

    public function setMetadata($value)
    {
        return $this->setParameter('metadata', $value);
    }

    public function getFees()
    {
        return $this->getParameter('fees');
    }

    public function setFees($value)
    {
        return $this->setParameter('fees', $value);
    }

    public function getClearing()
    {
        return $this->getParameter('clearing');
    }

    public function setClearing($value)
    {
        return $this->setParameter('clearing', $value);
    }

    public function getCorrelationId()
    {
        return $this->getParameter('correlationId');
    }

    public function setCorrelationId($value)
    {
        return $this->setParameter('correlationId', $value);
    }

    public function getAchDetails()
    {
        return $this->getParameter('achDetails');
    }

    public function setAchDetails($value)
    {
        return $this->setParameter('achDetails', $value);
    }

    public function getMassPayments()
    {
        return $this->getParameter('massPayments');
    }

    public function setMassPayments($value)
    {
        return $this->setParameter('massPayments', $value);
    }

    /**
     * Create a purchase using Dwolla's off-site gateway.
     *
     * @param array $parameters
     * @return \Omnipay\Dwolla\Message\PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Dwolla\Message\PurchaseRequest', $parameters);
    }
}
