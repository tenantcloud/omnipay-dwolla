<?php

namespace Omnipay\Dwolla\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Dwolla\GetterSetterTrait;

/**
 * Dwolla Abstract Request
 * @method \Omnipay\Dwolla\Message\Response send()
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    use GetterSetterTrait;

    /**
     * @param $method
     * @param $endpoint
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendRequest($method, $endpoint, $data)
    {
        $headers = [
            'Authorization' => $this->getBearer(),
            'Accept' => 'application/vnd.dwolla.v1.hal+json',
            'Content-Type' => 'application/json'
        ];
        $body = $data ? json_encode($data) : null;
        $httpResponse = $this->httpClient->request($method, $this->getHost() . $endpoint, $headers, $body);

        return $httpResponse;
    }
}
