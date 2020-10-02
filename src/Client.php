<?php


namespace Saulmoralespa\RazerGoldPin;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;


class Client
{
    const SANDBOX_URL_BASE = 'https://sandbox-api.mol.com/';
    const URL_BASE = 'https://api.mol.com/';
    const API_VERSION = 'v1';

    protected static $_sandbox = false;
    private $applicationCode;
    private $secretKey;
    private $userName;

    public function __construct($applicationCode, $secretKey, $userName)
    {
        $this->applicationCode = $applicationCode;
        $this->secretKey = $secretKey;
        $this->userName = $userName;
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
            return self::SANDBOX_URL_BASE;
        return self::URL_BASE;
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
            $response = $this->client()->post("pinstore/purchaseinitiation", [
                "headers" => [
                    "Content-Type" => "application/x-www-form-urlencoded"
                ],
                "form_params" => $params
            ]);
            $result = self::responseJson($response);
            self::checkErros($result);
            return $result;
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
                "signature" => $this->generateSignature($params)
            ]);
            $response = $this->client()->post("pinstore/purchaseconfirmation", [
                "headers" => [
                    "Content-Type" => "application/x-www-form-urlencoded"
                ],
                "form_params" => $params
            ]);
            $result = self::responseJson($response);
            self::checkErros($result);
            return $result;

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

    private function generateSignature(array $params)
    {
        if (isset($params['productCode']))
            $signature = $this->applicationCode . $params['productCode'] . $params['quantity'] . $params['referenceId'] . self::API_VERSION . $this->secretKey;
        if (isset($params['validatedToken']))
            $signature = $this->applicationCode . $params['referenceId'] . self::API_VERSION . $params['validatedToken'] . $this->secretKey;
        if (isset($params['sku']))
            $signature = $this->applicationCode . $this->userName . $params['sku'] . $params['referenceId'] . self::API_VERSION  . $this->secretKey;

        return md5($signature);
    }

    private static function checkErros($result)
    {
        $code = $result->initiationResultCode ?? $result->purchaseStatusCode;
        $erros = self::getMessageError();
        if($code !== '00')
            throw new \Exception($erros[$code]);
    }

    public static function getMessageError()
    {
        return [
            '01' => 'Payment has not complete or in middle of processing',
            '02' => 'None or partial stock delivered due to out of stock',
            '04' => 'Insufficient merchant’s fund to perform request',
            '05' => 'Invalid Product Code or not supported by merchant or RAZER GOLD',
            '06' => 'The same reference id has been used for Pin Purchase Initiation Request',
            '07' => 'Certain product will check on country and IP address restriction settings from 3rd party product settings',
            '99' => 'Purchase transaction is failed'
        ];
    }

}