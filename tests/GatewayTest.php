<?php

namespace Omnipay\Dwolla;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class GatewayTest
 * @package Omnipay\Dwolla
 */
class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testTransferMinimalDataPurchaseSuccess()
    {
        $request = $this->gateway->purchase([
            'amount' => '5.00',
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544'
        ]);

        $data = $request->getData();

        $this->assertInstanceOf('Omnipay\Dwolla\Message\PurchaseRequest', $request);
        $this->assertArrayHasKey('_links', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('clearing', $data);
        $this->assertArrayNotHasKey('sourceType', $data);
        $this->assertArrayNotHasKey('destinationType', $data);
        $this->assertArrayNotHasKey('metadata', $data);
        $this->assertArrayNotHasKey('fees', $data);
        $this->assertArrayNotHasKey('achDetails', $data);
        $this->assertArrayNotHasKey('correlationId', $data);
        $this->assertArrayNotHasKey('massPayments', $data);
        $this->assertSame('5.00', $request->getAmount());
        $this->assertSame('c70a7dde-c9b8-42cc-ba51-612908434eb4', $request->getSourceId());
        $this->assertSame('cf3415ec-8286-4e4f-b280-250074244544', $request->getDestinationId());
    }

    public function testTransferMaximumDataPurchaseSuccess()
    {
        $request = $this->gateway->purchase([
            'amount' => '10.00',
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'sourceType' => 'customers',
            'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544',
            'destinationType' => 'funding-sources',
            'correlationId' => 'testId',
            'metadata' => ['a1' => 'a2'],
            'fees' => ['b1' => 'b2'],
            'achDetails' => ['c1' => 'c2'],
        ]);

        $data = $request->getData();

        $this->assertInstanceOf('Omnipay\Dwolla\Message\PurchaseRequest', $request);
        $this->assertArrayHasKey('_links', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('clearing', $data);
        $this->assertArrayNotHasKey('massPayments', $data);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('c70a7dde-c9b8-42cc-ba51-612908434eb4', $request->getSourceId());
        $this->assertSame('customers', $request->getSourceType());
        $this->assertSame('cf3415ec-8286-4e4f-b280-250074244544', $request->getDestinationId());
        $this->assertSame('funding-sources', $request->getDestinationType());
        $this->assertArraySubset(['a1' => 'a2'], $request->getMetadata());
        $this->assertArraySubset(['b1' => 'b2'], $request->getFees());
        $this->assertArraySubset(['c1' => 'c2'], $request->getAchDetails());
        $this->assertSame('testId', $request->getCorrelationId());
    }

    public function testMassPaymentMinimalDataPurchaseSuccess()
    {
        $request = $this->gateway->purchase([
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'massPayments' => [
                [
                    'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544',
                    'amount' => '1.00'
                ],
                [
                    'destinationId' => 'f7fdf7b2-ebdb-479f-9db9-1fd3dd92a094',
                    'amount' => '2.00'
                ]
            ]

        ]);

        $data = $request->getData();

        $this->assertInstanceOf('Omnipay\Dwolla\Message\PurchaseRequest', $request);
        $this->assertArrayHasKey('_links', $data);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('clearing', $data);
        $this->assertArrayNotHasKey('sourceType', $data);
        $this->assertArrayNotHasKey('destinationId', $data);
        $this->assertArrayNotHasKey('destinationType', $data);
        $this->assertArrayNotHasKey('metadata', $data);
        $this->assertArrayNotHasKey('fees', $data);
        $this->assertArrayNotHasKey('achDetails', $data);
        $this->assertArrayNotHasKey('correlationId', $data);

        $this->assertSame('standard', $request->getClearing());
        $this->assertSame('c70a7dde-c9b8-42cc-ba51-612908434eb4', $request->getSourceId());
        $this->assertTrue(is_array($request->getMassPayments()));

        foreach ($data['items'] as $i => $item) {
            $expected = $request->getMassPayments()[$i];
            $this->assertArrayNotHasKey('source', $item['_links']);
            $this->assertContains($expected['destinationId'], $item['_links']['destination']['href']);
            $this->assertSame($expected['amount'], $item['amount']['value']);
            $this->assertSame($request->getClearing(), $item['clearing']['source']);
        }
    }

    public function testMassPaymentEmptyAmountPurchaseFailed()
    {
        $this->expectException(InvalidRequestException::class);
        $request = $this->gateway->purchase([
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'massPayments' => [
                [
                    'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544'
                ]
            ]
        ]);
        $request->getData();
    }

    public function testMassPaymentEmptyDestinationIdPurchaseFailed()
    {
        $this->expectException(InvalidRequestException::class);
        $request = $this->gateway->purchase([
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'massPayments' => [
                [
                    'amount' => '1.00'
                ]
            ]
        ]);
        $request->getData();
    }

    public function testMassPaymentMaximalDataPurchaseSuccess()
    {
        $request = $this->gateway->purchase([
            'sourceId' => 'c70a7dde-c9b8-42cc-ba51-612908434eb4',
            'sourceType' => 'customers',
            'correlationId' => 'testId',
            'metadata' => ['a1' => 'a2'],
            'fees' => ['b1' => 'b2'],
            'achDetails' => ['c1' => 'c2'],
            'massPayments' => [
                [
                    'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544',
                    'destinationType' => 'funding-sources',
                    'metadata' => ['d1' => 'd2'],
                    'amount' => '1.00',
                    'correlationId' => 'testId2'
                ],
                [
                    'destinationType' => 'funding-sources',
                    'destinationId' => 'cf3415ec-8286-4e4f-b280-250074244544',
                    'metadata' => ['e1' => 'e2'],
                    'amount' => '2.00',
                    'correlationId' => 'testId3'
                ]
            ]

        ]);

        $data = $request->getData();

        $this->assertInstanceOf('Omnipay\Dwolla\Message\PurchaseRequest', $request);
        $this->assertArrayHasKey('_links', $data);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('clearing', $data);
        $this->assertArrayHasKey('metadata', $data);
        $this->assertArrayHasKey('fees', $data);
        $this->assertArrayHasKey('achDetails', $data);
        $this->assertArrayHasKey('correlationId', $data);

        $this->assertTrue(count($data['_links']) === 1);
        $this->assertSame('standard', $request->getClearing());
        $this->assertSame('c70a7dde-c9b8-42cc-ba51-612908434eb4', $request->getSourceId());
        $this->assertSame('customers', $request->getSourceType());
        $this->assertArraySubset(['a1' => 'a2'], $request->getMetadata());
        $this->assertArraySubset(['b1' => 'b2'], $request->getFees());
        $this->assertArraySubset(['c1' => 'c2'], $request->getAchDetails());
        $this->assertSame('testId', $request->getCorrelationId());

        $this->assertTrue(is_array($request->getMassPayments()));

        foreach ($data['items'] as $i => $item) {
            $expected = $request->getMassPayments()[$i];
            $this->assertArrayNotHasKey('source', $item['_links']);
            $this->assertContains($expected['destinationId'], $item['_links']['destination']['href']);
            $this->assertSame($expected['amount'], $item['amount']['value']);
            $this->assertSame($request->getClearing(), $item['clearing']['source']);
        }
    }
}