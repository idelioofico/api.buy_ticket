<?php

namespace App\DTO;

class BaseDTO
{

    public function dispatch($url, $method = 'GET', $headers, $body)
    {
        // dd($body);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers))
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ($body));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);


        $resp = curl_exec($curl);
        curl_close($curl);

        return json_decode($resp);
    }
}
