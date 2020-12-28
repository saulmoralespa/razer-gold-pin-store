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
        $this->razer->sandboxMode(true);

    }

    public function testpurchaseInitiation()
    {
        $params = [
            "referenceId" => 'inv' . time(),
            "productCode" => "0228542",
            "quantity"  => 1,
            //"merchantProductCode" => "STEAM-MXN100",
            //"consumerCountryCode" => "MX"
        ];
        $response = $this->razer->purchaseInitiation($params);
        $this->assertAttributeNotEmpty('validatedToken', $response);
    }

    public function testPurchaseConfirmation()
    {
        $params = [
            "referenceId" => "inv1601654605",
            "validatedToken" => "549a93e387364247ade5db6a9dc55e8d"
        ];
        $response = $this->razer->purchaseConfirmation($params);
        $this->assertAttributeEquals('00', 'purchaseStatusCode', $response);
    }

    public function testGetConfirmation()
    {
        $params = [
            "referenceId" => "inv1604943764",
            "validatedToken" => "8c5c88d078f24910948507c2ec352584"
        ];

        $response = $this->razer->getConfirmation($params);
        var_dump($response);
    }
}