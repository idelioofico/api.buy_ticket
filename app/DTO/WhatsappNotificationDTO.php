<?php

namespace App\DTO;

use App\Http\Controllers\LogController;
use App\Models\Configuration;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class WhatsappNotificationDTO
{

    protected $apiKey;
    protected $serviceUrl;

    public function notify($mobile_number, $message, $reset = 0)
    {
        $url = (Setting::where('type', 'whatsapp_notify_url')->first())->value;

        $curl = curl_init();

        $payload = '{
            "phone_number":"' . $mobile_number . '",
            "message":"' . $message . '"
        }';

        curl_setopt_array($curl, array(
            CURLOPT_URL => trim($url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Encoding: application/json',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $ip = curl_getinfo($curl, CURLINFO_PRIMARY_IP);

        curl_close($curl);
        $data = json_decode($response);

            $log = new LogController();
            $log->save_log($status, 'Whatsapp', 'Notification', json_encode($payload), json_encode($data), $ip);

            return  $data;
    }

    public function submitRequest($url, $method, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => trim($url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Accept-Encoding: application/json',
                'Content-Type: application/json'
            )

        ));

        $response = curl_exec($curl);

        return array('status' => curl_getinfo($curl, CURLINFO_HTTP_CODE), 'data' => $response);
    }

}
