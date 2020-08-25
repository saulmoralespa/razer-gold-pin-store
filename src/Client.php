<?php


namespace Saulmoralespa\RazerGoldPin;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;


class Client
{
    const SANDBOX_URL_PURCHASEINITIATION = 'https://sandbox-api.mol.com/pinstore/';
    const URL_PURCHASEINITIATION = 'https://api.mol.com/pinstore/';
    const API_VERSION = 'v1';

    protected static $_sandbox = false;
    private $applicationCode;
    private $secretKey;

    public function __construct($applicationCode, $secretKey)
    {
        $this->applicationCode = $applicationCode;
        $this->secretKey = $secretKey;
    }

    public function client()
    {
        return new GuzzleClient([
            "base_uri" => $this->getBaseUrl()
        ]);
    }

    public function getBaseUrl()
    {
        if(self::$_sandbox)
            return self::SANDBOX_URL_PURCHASEINITIATION;
        return self::URL_PURCHASEINITIATION;
    }

    public function sandboxMode($status = false)
    {
        self::$_sandbox = $status;
    }


    public function purchaseInitiation(array $params)
    {
        try {
            $params = array_merge($params, [
                "applicationCode" => $this->applicationCode,
                "version" => self::API_VERSION,
                "signature" => $this->generateSignature($params)
            ]);
            $response = $this->client()->post("purchaseinitiation", [
                "headers" => [
                    "Content-Type" => "application/x-www-form-urlencoded"
                ],
                "form_params" => $params
            ]);
            return self::responseJson($response);
        }catch(RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function purchaseConfirmation(array $params)
    {
        try {
            $params = array_merge($params, [
                "applicationCode" => $this->applicationCode,
                "version" => self::API_VERSION,
                "signature" => $this->generateSignature($params, true)
            ]);
            $response = $this->client()->post("purchaseconfirmation", [
                "headers" => [
                    "Content-Type" => "application/x-www-form-urlencoded"
                ],
                "form_params" => $params
            ]);
            return self::responseJson($response);
        }catch(RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public static function responseJson($response)
    {
        return \GuzzleHttp\json_decode(
            $response->getBody()->getContents()
        );
    }

    private function generateSignature(array $params, $isConfirmation = false )
    {
        if ($isConfirmation)
            return md5($this->applicationCode . $params['referenceId'] . self::API_VERSION . $params['validatedToken'] . $this->secretKey);
        return md5($this->applicationCode . $params['productCode'] . $params['quantity'] . $params['referenceId'] . self::API_VERSION . $this->secretKey);
    }

}