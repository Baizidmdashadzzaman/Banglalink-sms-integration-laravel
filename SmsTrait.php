<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Controllers\APIResponse;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Http;

trait SmsTrait {

    /**
     * @param Request $request
     * @return $this|false|string
     */
    protected $baseUrl = 'https://corpsms.banglalink.net/bl/api/v1/smsapigw/';

    public function send_sms_banglalink($phoneno,$message)
    {
        $secureRandomNumber = random_int(100000, 999999);
        $clienttransid = $secureRandomNumber.time();
        $response = Http::post($this->baseUrl, [
            'username' => '',
            'password' => '',
            'apicode' => '5',
            'msisdn' => [$phoneno],
            'countrycode' => '880',
            'cli' => '',//same as user name
            'messagetype' => '1',
            'message' => $message,
            'clienttransid' => $clienttransid,
            'bill_msisdn' => ,//phone number that created account
            'tran_type' => 'T',
            'request_type' => 'S',
            'rn_code' => '91'
        ]);
        return $response->json();
    }

     public function checkBalance()
     {
         $payload = [
             'username' => 'Enterprise_User',
             'password' => 'Ent@12345',
             'apicode' => '2',
             'cli' => 'TESTCLI',
             'clienttransid' => uniqid()
         ];
 
         $response = Http::post('https://corpsms.banglalink.net/bl/ecmapigw/webresources/ecmapigw.v3', $payload)->json();
         return $this->handleResponse($response);
     }
 
     public function checkDeliveryStatus($clientTransId)
     {
         $payload = [
             'username' => 'Enterprise_User',
             'password' => 'Entp@12345',
             'apicode' => '4',
             'clienttransid' => $clientTransId,
         ];
 
         $response = Http::post('https://corpsms.banglalink.net/bl/ecmapigw/webresources/ecmapigw.v3', $payload)->json();
         return $this->handleResponse($response);
     }
 
      public function handleResponse($response)
     {
         $statusCode = $response['statusInfo']['statusCode'] ?? null;
 
         switch ($statusCode) {
             case '1000':
                 return 'Success';
             case '1005':
                 return 'Invalid Parameter';
             case '1002':
                 return 'Invalid Username';
             case '1003':
                 return 'Invalid Password';
             case '1008':
                 return 'Insufficient Balance';
             default:
                 return $response['statusInfo']['errordescription'] ?? 'Unknown error';
         }
     }

}
