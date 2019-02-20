<?php

namespace Omnipay\Dwolla\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PurchaseRequestTest
 * @package Omnipay\Dwolla\Message
 */
class PurchaseRequestTest extends TestCase
{
    /**
     * @var
     */
    private $request;

    public function setUp()
    {
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request = $request->initialize([
            'autToken' => 'secret',
            'amount' => '5.00',
            'testMode' => true,
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544',
            'metadata' => [
                'checkoutId' => '2346778'
            ],
            'clearing' => [
            	'source' => 'standard'
			]
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('https://api-sandbox.dwolla.com/funding-sources/c70a7dde-c9b8-42cc-ba51-612908434eb4',
            $data['_links']['source']['href']);
        $this->assertSame('https://api-sandbox.dwolla.com/funding-sources/cf3415ec-8286-4e4f-b280-250074244544',
            $data['_links']['destination']['href']);
        $this->assertSame('USD', $data['amount']['currency']);
        $this->assertSame('5.00', $data['amount']['value']);
        $this->assertSame('2346778', $data['metadata']['checkoutId']);
        $this->assertSame('standard', $data['clearing']['source']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(201, $response->getCode());
        $this->assertSame(null, $response->getMessage());
        $this->assertSame('186ed991-6fc8-e811-8110-d08b405a9c82',
            $response->getTransactionReference());
    }

    public function testSendMassPaymentsSuccess()
    {
        $this->setMockHttpResponse('PurchaseMassPaymentsSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(201, $response->getCode());
        $this->assertSame(null, $response->getMessage());
        $this->assertSame('7838cba4-2510-4efa-8f19-a9700140fb03',
            $response->getTransactionReference());
    }

    public function testSendAmountFailure()
    {
        $this->setMockHttpResponse('PurchaseAmountFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Invalid amount.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendDestinationFailure()
    {
        $this->setMockHttpResponse('PurchaseDestinationFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Invalid destination.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendFundingSourceNotFoundFailure()
    {
        $this->setMockHttpResponse('PurchaseFundingSourceNotFoundFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Funding source not found.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendBanSyntaxFailure()
    {
        $this->setMockHttpResponse('PurchaseBanSyntaxFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('The request body contains bad syntax or is incomplete.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendInsufficientFundsFailure()
    {
        $this->setMockHttpResponse('PurchaseInsufficientFundsFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Insufficient funds.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendMetadataFailure()
    {
        $this->setMockHttpResponse('PurchaseMetadataFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Metadata not supported for this type of transfer.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendJsonExceptionFailure()
    {
        $this->setMockHttpResponse('PurchaseJsonBrokenFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Ooops! Service is not available.', $response->getMessage());
        $this->assertTrue(json_last_error() > 0);
        $this->assertNull($response->getTransactionReference());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->getCode() >= 400);
        $this->assertSame('Ooops! Service is not available.', $response->getMessage());
        $this->assertNull($response->getTransactionReference());
    }
}
