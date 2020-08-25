<?php

use Saulmoralespa\RazerGoldPin\Client;
use PHPUnit\Framework\TestCase;


class RazerGoldPinStoreTest extends TestCase
{
    public $razer;

    protected function setUp()
    {
        $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
        $dotenv->load();

        $applicationCode = $_ENV['APPLICATION_CODE'];
        $secretKey = $_ENV['SECRET_KEY'];

        $this->razer = new Client($applicationCode, $secretKey);
        $this->razer->sandboxMode(false);

    }

    public function testpurchaseInitiation()
    {
        $params = [
            "referenceId" => 'inv' . time(),
            "productCode" => "STEAM-MXN100",
            "quantity"  => 1,
            "merchantProductCode" => "STEAM-MXN100", //P33128391  STEAM-CLP50000
            "consumerCountryCode" => "MX"
        ];
        $response = $this->razer->purchaseInitiation($params);
        var_dump($response);
    }

    /*public function testPurchaseConfirmation()
    {
        $params = [
            "referenceId" => "inv1597420184",
            "validatedToken" => ""
        ];
        $response = $this->razer->purchaseConfirmation($params);
        var_dump($response);
    }*/
}