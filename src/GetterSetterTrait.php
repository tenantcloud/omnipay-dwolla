<?php

namespace Omnipay\Dwolla;

/**
 * Dwolla GetterSetterTrait
 * @link https://developers.dwolla.com/
 */
trait GetterSetterTrait
{
    public $host = 'https://api.dwolla.com';
    public $sandbox_host = 'https://api-sandbox.dwolla.com';

    public $clearing = 'standard';

    public $endpointTransfers = '/transfers';
    public $endpointMassPayments = '/mass-payments';

    public $sourceType = 'funding-sources';
    public $destinationType = 'funding-sources';

    public function getHost(): string
    {
        return $this->getTestMode() ? $this->sandbox_host : $this->host;
    }

    public function getEndPoint(): string
    {
        return $this->getMassPayments() ? $this->endpointMassPayments : $this->endpointTransfers;
    }

    public function getBearer(): string
    {
        return 'Bearer ' . $this->getAppToken();
    }

    public function isMassPayment(): bool
    {
        return (bool)count($this->getMassPayments());
    }

    public function getAppToken()
    {
        return $this->getParameter('appToken');
    }

    public function setAppToken($value)
    {
        return $this->setParameter('appToken', $value);
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
        return $this->getParameter('sourceType') ?? $this->sourceType;
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
        return $this->getParameter('destinationType') ?? $this->destinationType;
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
        return $this->getParameter('clearing') ?? $this->clearing;
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
}
