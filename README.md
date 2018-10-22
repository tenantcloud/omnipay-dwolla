# Omnipay: Dwolla v2

**Dwolla v2 gateway support for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/Dwolla/omnipay-dwolla.svg)](https://travis-ci.org/Dwolla/omnipay-dwolla)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Dwolla support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). omnipay-dwolla is currently not a part
of the official Omnipay branch; we are working on this.

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Dwolla (off-site gateway)

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

[Dwolla API documentation is available here.](https://developers.dwolla.com)

----

In order to get started, first [obtain Dwolla API credentials](https://accounts-sandbox.dwolla.com/sign-up)

### Initiating a Checkout

Off-site gateway checkouts are initiated by calling the `purchase()` method. 
```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Dwolla');

$gateway->setAppToken('An oauth token!');

// Want sandbox mode?
// $gateway->setTestMode(true);

// One transaction
$response = $gateway->purchase([
    'sourceId' => 'some_uuid',
    'destinationId' => 'some_uuid',
    'amount' => '10.00',
    'metadata' => [
        'order' => 'order_number'
    ]
])->send();

// Or mas payments
$response = $gateway->purchase([
    'sourceId' => 'source_uuid_1',
    'sourceType' => 'funding-sources',
    'massPayments' => [
        [
            'destinationId' => 'destination_uuid_1',
            'amount' => '0.01'
        ],
        [
            'destinationId' => 'destination_uuid_2',
            'amount' => '0.02'
        ]
    ],
    'metadata' => [
        'order' => 'order_number'
    ]
])->send();

// Response example
[
    'success' => true,
    'statusCode' => 201,
    'referenceId' => 'https://api-sandbox.dwolla.com/transfers/282233ca-f85d-40e8-9ca6-a97d00ddd612',
    'massPayment' => false,
    'message' => null,
    'errors' => null
]

if ($response->isSuccessful()) {
    // Get response transaction link
    $link = $response->getTransactionReference();
} else {
    // Something went wrong!
    echo $response->getMessage();
}
```


## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/tenantcloud/omnipay-dwolla/issues),
or better yet, fork the library and submit a pull request.
