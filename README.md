# Schibsted Payment Gateway SDK

[![Travis](https://img.shields.io/travis/schibsted/sdk-php-payment-gateway.svg)](https://travis-ci.org/schibsted/sdk-php-payment-gateway)

## Install with composer

 - `composer require schibsted/sdk-php-payment-gateway`

## Usage

```php
<?php
use schibsted\payment\lib\Connections;
use schibsted\payment\sdk\response\Success;
use schibsted\payment\resources\Order;
use schibsted\payment\sdk\Auth;

Connections::config(Connections::ENV_PRE, [
    'client_id'         => '<YOUR ID>',
    'secret'            => '<YOUR SECRET>',
    'redirect_uri'      => '<YOUR REDIRECT URI>',
    'adapter_config' => [CURLOPT_CONNECTTIMEOUT_MS => 1000]
]);
Connections::setEnv(Connections::ENV_PRE);

/***/

$connection = Connections::get();
$tokens = (new Auth(compact('connection')))->getToken(); // Store in session or other cache

/***/

$connection['token'] =  $tokens['access_token'];

$amount = 12000; // 120 euro, amount in CENT
$vat = 2500; // 25%, amount in hundredths

$order_res = new Order(compact('connection'));
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
