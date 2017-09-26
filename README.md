# Schibsted Payment Gateway SDK

[![Travis](https://img.shields.io/travis/schibsted/sdk-php-payment-gateway.svg)](https://travis-ci.org/schibsted/sdk-php-payment-gateway)

## Install

Add this to your `composer.json`

```json
{
    "require": {
        "schibsted/sdk-php-payment-gateway": "*",
        "schibsted/sdk-php": "*"
    },
    "repositories": [
        {
          "type": "git",
          "url": "https://github.com/schibsted/sdk-php-payment-gateway.git"
        }
    ]
}
```

## Usage

```php
<?php
use schibsted\payment\sdk\response\Success;
use schibsted\payment\sdk\response\Response;
use schibsted\payment\resources\Order;

$spid_config = [
    VGS_Client::CLIENT_ID       => '<YOUR ID>',
    VGS_Client::CLIENT_SECRET   => '<YOUR SECRET>',
    VGS_Client::CLIENT_SIGN_SECRET => '<YOUR SIGNATURE SECRET>',
    VGS_Client::PRODUCTION      => false,
    VGS_Client::API_VERSION     => 2,
];
$payment_gateway_config = [
	'host' => 'https://api-gateway-stage.payment.schibsted.no',
	'port' => '443',
    'adapter_config' => [ CURLOPT_CONNECTTIMEOUT_MS => 1000]
];

$spid_client = new VGS_Client($spid_config);
$access_token = $spid_client->auth();

$payment_gateway_config['adapter_config'][CURLOPT_HTTPHEADER] = ['oauth_token:' . $access_token];

$amount = 12000; // 120 euro, amount in CENT
$vat = 2500; // 25%, amount in hundredths

$order_res = new Order($payment_gateway_config);
$data = [
    "purchaseFlow" => "SALE",
    "fromPaymentProviderInfo" => [
        [
            "ppaProvider" => "ADYEN_CARD",
            "storedPaymentMethodId" => $method_id
        ]
    ],
    "description" => "Some description of a payment",
    "fromUserIp" => "127.0.0.1",
    "currency" => "EUR",
    "clientId" => $this->client_id,
    "merchantId" => $merchant_id,
    "transactionReference" => uniqid(),
    "orderItems" => [
        [
            "quantity" => 1,
            "type" => "DEFAULT",
            "price" => $amount,
            "vat" => $vat,
            "name" => "Some name of item",
            "description" => 'Some description of item',
        ]
    ]
];
$result = $order_res->create($data);
if ($result instanceof Success === false) {
    $content = $result->getContent();
    $c = $content['errorCode'] ?? '-';
    $m = $content['errorMessage'] ?? 'unknown';
    $r = $content['requestId'] ?? '<NO REQUEST ID>';
    throw new \Exception("Create Order failed: $c : $r : $m");
}
$order = $result->getContent();
$order_id = $order['id'];
if (is_numeric($order_id) == false) {
    throw new \Exception("Order object not valid");
}
$result = $order_res->initialize($order_id);
if ($result instanceof Success === false) {
    $content = $result->getContent();
    $c = $content['errorCode'] ?? '-';
    $m = $content['errorMessage'] ?? 'unknown';
    $r = $content['requestId'] ?? '<NO REQUEST ID>';
    throw new \Exception("Order Init failed: $c : $r : $m");
}
$result = $order_res->complete($order_id);
if ($result instanceof Success === false) {
    $content = $result->getContent();
    $c = $content['errorCode'] ?? '-';
    $m = $content['errorMessage'] ?? 'unknown';
    $r = $content['requestId'] ?? '<NO REQUEST ID>';
    throw new \Exception("Order Complete failed: $c : $r : $m");
}
return $result->getContent();

```
