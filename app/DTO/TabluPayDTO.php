<?php

namespace App\DTO;

use App\Http\Controllers\LogController;
use App\Models\Configuration;
use App\Models\Setting;
use Exception;

class TabluPayDTO
{

    protected $key;
    protected $url;
    protected $transaction_reference;

    public function __construct()
    {
        $this->key = (Setting::where('type', 'service_key_tablupay')->first())->value;
        $this->url = (Setting::where('type', 'service_url_tablupay')->first())->value;
    }

    public  function submit_payment($request)
    {
        $fields = array(
            'transaction_reference' => strval($request['reference']),
            'third_party_reference' => strval($request['reference']),
            'msisdn' => strval($request['msisdn']),
            'amount' => strval($request['amount']),
            'b_id' => strval($request['business'])
        );

        if (isset($request['content'])) {
            $fields['content_text'] = $request['content'];
        }

        if (isset($request['language'])) {
            $fields['language'] = $request['language'];
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => trim($this->url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$fields,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$this->key}"
            ),
        ));

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = curl_exec($curl);
        $ip = curl_getinfo($curl, CURLINFO_PRIMARY_IP);

        curl_close($curl);
        $data = json_decode($response);
        $log = new LogController();
        $log->save_log($status, 'TabluPay', 'Wallet Request', json_encode($fields), json_encode($data), $ip);

        return  $data;
    }
}
