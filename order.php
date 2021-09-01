<?php

require './vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Enum\CountryCodeIso3166;
use RetailCrm\Api\Enum\Customers\CustomerType;
use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Model\Entity\Orders\Delivery\OrderDeliveryAddress;
use RetailCrm\Api\Model\Entity\Orders\Delivery\SerializedOrderDelivery;
use RetailCrm\Api\Model\Entity\Orders\Items\Offer;
use RetailCrm\Api\Model\Entity\Orders\Items\OrderProduct;
use RetailCrm\Api\Model\Entity\Orders\Items\PriceType;
use RetailCrm\Api\Model\Entity\Orders\Items\Unit;
use RetailCrm\Api\Model\Entity\Orders\Order;
use RetailCrm\Api\Model\Entity\Orders\Payment;
use RetailCrm\Api\Model\Entity\Orders\SerializedRelationCustomer;
use RetailCrm\Api\Model\Request\Orders\OrdersCreateRequest;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');


$client = SimpleClientFactory::createClient('https://superposuda.retailcrm.ru', $_ENV["API_KEY"]);

$request         = new OrdersCreateRequest();
$order           = new Order();
$payment         = new Payment();
$delivery        = new SerializedOrderDelivery();
$deliveryAddress = new OrderDeliveryAddress();
$offer           = new Offer();
$item            = new OrderProduct();

$payment->type   = 'bank-card';
$payment->status = 'paid';
$payment->amount = 1000;
$payment->paidAt = new DateTime();

$deliveryAddress->index      = '344001';
$deliveryAddress->countryIso = CountryCodeIso3166::RUSSIAN_FEDERATION;
$deliveryAddress->region     = 'Region';
$deliveryAddress->city       = 'City';
$deliveryAddress->street     = 'Street';
$deliveryAddress->building   = '10';

$delivery->address = $deliveryAddress;
$delivery->cost    = 0;
$delivery->netCost = 0;

$offer->name        = 'Offer №08091991';
$offer->displayName = 'Offer №08091991';
$offer->xmlId       = 'tGunLo27jlPGmbA8BrHxY2';
$offer->article     = 'Маникюрный набор AZ105R Azalita';
$offer->unit        = new Unit('AZ105R', 'Azalita', 'pcs');

$item->offer         = $offer;
$item->priceType     = new PriceType('base');
$item->quantity      = 1;
$item->purchasePrice = 60;

$order->delivery      = $delivery;
$order->items         = [$item];
$order->payments      = [$payment];
$order->orderType     = 'fizik';
$order->orderMethod   = 'test';
$order->countryIso    = CountryCodeIso3166::RUSSIAN_FEDERATION;
$order->firstName     = 'Andrey';
$order->lastName      = 'Grigoriev';
$order->patronymic    = 'Sergeevich';

$order->customer      = SerializedRelationCustomer::withIdAndType(
    4924,
    CustomerType::CUSTOMER
);


$order->status        = 'assembling';
$order->statusComment = 'https://github.com/grigoriiev/retailTask';
$order->weight        = 1000;
$order->shipmentStore = 'main12';
$order->shipmentDate  = (new DateTime())->add(new DateInterval('P7D'));
$order->shipped       = false;
$order->customFields  = [
    "magazine" => 'test',
    "prim"=>"тестовое задание"
];

$request->order = $order;
$request->site  = 'test';

try {
    $response = $client->orders->create($request);
} catch (ApiExceptionInterface $exception) {
    echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
    exit(-1);
} catch (ClientExceptionInterface $exception) {
    echo $exception; // Every ApiExceptionInterface instance should implement __toString() method.
    exit(-1);
}

printf(
    'Created order id = %d with the following data: %s',
    $response->id,
    print_r($response->order, true)
);
