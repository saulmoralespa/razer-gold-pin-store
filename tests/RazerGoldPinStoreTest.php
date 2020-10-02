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
        $userName = $_ENV['USER_NAME'];

        $this->razer = new Client($applicationCode, $secretKey, $userName);
        $this->razer->sandboxMode(true);

    }

    public function testpurchaseInitiation()
    {
        $params = [
            "referenceId" => 'inv' . time(),
            "productCode" => "0217475",
            "quantity"  => 1,
            //"merchantProductCode" => "STEAM-MXN100",
            "consumerCountryCode" => "MX"
        ];
        $response = $this->razer->purchaseInitiation($params);
        var_dump($response);
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
}