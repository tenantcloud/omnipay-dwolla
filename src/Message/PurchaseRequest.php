<?php

namespace Omnipay\Dwolla\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class PurchaseRequest
 * AbstractRequest, implements purchase().
 * @package Omnipay\Dwolla\Message
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * Initializes data parameters from OmniPay fields
     * to fields recognizable by the Dwolla API.
     *
     * For more details on what values are accepted
     * by Dwolla, visit: https://docsv2.dwolla.com/#initiate-a-transfer
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        if ($this->isMassPayment()) {
            $data = $this->getDataForMassPayments();
        } else {
            $data = $this->getDataForTransfer();
        }

        return $data;
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getDataForTransfer(): array
    {
        $this->validate('amount', 'sourceId', 'destinationId');

        $data = [
            '_links' => [
                'source' => [
                    'href' => $this->getHost() . '/' . $this->getSourceType() . '/' . $this->getSourceId(),
                ],
                'destination' => [
                    'href' => $this->getHost() . '/' . $this->getSourceType() . '/' . $this->getDestinationId()
                ]
            ],
            'amount' => [
                'currency' => 'USD',
                'value' => $this->getAmount()
            ],
            'clearing' => [
                'source' => $this->getClearing()
            ]
        ];

        $metadata = $this->getMetadata();
        if (!is_null($metadata)) {
            $data['metadata'] = $metadata;
        }

        $fees = $this->getFees();
        if (!is_null($fees)) {
            $data['fees'] = $fees;
        }

        $correlationId = $this->getCorrelationId();
        if (!is_null($correlationId)) {
            $data['correlationId'] = $correlationId;
        }

        $achDetails = $this->getAchDetails();
        if (!is_null($achDetails)) {
            $data['achDetails'] = $achDetails;
        }

        return $data;
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getDataForMassPayments(): array
    {
        $this->validate('sourceId', 'massPayments');

        $items = [];
        foreach ($this->getMassPayments() as $payment) {

            $amount = $payment['amount'] ?? null;
            $destinationId = $payment['destinationId'] ?? null;
            $destinationType = $payment['destinationType'] ?? 'funding-sources';

            if (!$amount) {
                throw new InvalidRequestException("The 'amount' parameter is required");
            }
            if (!$destinationId) {
                throw new InvalidRequestException("The 'destinationId' parameter is required");
            }

            $item = [
                '_links' => [
                    'destination' => [
                        'href' => $this->getHost() . '/' . $destinationType . '/' . $destinationId,
                    ]
                ],
                'amount' => [
                    'currency' => 'USD',
                    'value' => $amount
                ],
                'clearing' => [
                    'source' => $this->getClearing()
                ]
            ];

            $metadata = $payment['metadata'] ?? null;
            if (!is_null($metadata)) {
                $item['metadata'] = $metadata;
            }

            $correlationId = $payment['correlationId'] ?? null;
            if (!is_null($correlationId)) {
                $item['correlationId'] = $correlationId;
            }

            $items[] = $item;
        }

        $data = [
            '_links' => [
                'source' => [
                    'href' => $this->getHost() . '/' . $this->getSourceType() . '/' . $this->getSourceId(),
                ]
            ],
            'items' => $items,
            'clearing' => [
                'source' => $this->getClearing()
            ]
        ];

        $metadata = $this->getMetadata();
        if (!is_null($metadata)) {
            $data['metadata'] = $metadata;
        }

        $fees = $this->getFees();
        if (!is_null($fees)) {
            $data['fees'] = $fees;
        }

        $correlationId = $this->getCorrelationId();
        if (!is_null($correlationId)) {
            $data['correlationId'] = $correlationId;
        }

        $achDetails = $this->getAchDetails();
        if (!is_null($achDetails)) {
            $data['achDetails'] = $achDetails;
        }

        return $data;
    }

    /**
     * Sends the checkout request to the Dwolla v2 API.
     *
     * @param mixed $data
     * @return Response
     */
    public function sendData($data): Response
    {
        $httpResponse = $this->sendRequest('POST', $this->getEndPoint(), $data);
        $statusCode = $httpResponse->getStatusCode();

        $response = [
            'success' => false,
            'statusCode' => $statusCode,
            'referenceId' => null,
            'massPayment' => $this->isMassPayment(),
            'message' => null,
            'errors' => null
        ];
        switch ($statusCode) {
            case 201:
                $ref = $httpResponse->getHeader('location');
                if ($ref && is_array($ref) && count($ref)) {
                    $response['success'] = true;
                    $response['referenceId'] = reset($ref);
                }
                break;
            default:
                $response = $this->parseErrorResponse($httpResponse, $response);
                break;
        }

        return $this->response = new Response($this, $response);
    }

    /**
     * Prepare error response
     *
     * @param $httpResponse
     * @param $response
     * @return mixed
     */
    private function parseErrorResponse($httpResponse, $response): array
    {
        $errorResponse = json_decode($httpResponse->getBody()->getContents(), true);

        $response['message'] = 'Ooops! Service is not available.';

        if (!$errorResponse || !is_array($errorResponse) || !array_key_exists('code', $errorResponse)) {
            return $response;
        }

        switch ($errorResponse['code']) {
            case 'ValidationError':
                $response['message'] = $errorResponse['_embedded']['errors']['0']['message'];
                $response['errors'] = $errorResponse['_embedded']['errors'];
                break;
            case 'BadRequest':
                $response['message'] = $errorResponse['message'];
                break;
        }

        return $response;
    }
}
