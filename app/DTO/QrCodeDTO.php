<?php

namespace App\DTO;

use App\Models\Setting;
use Exception;

class QrCodeDTO extends BaseDTO{


    public function __construct()
    {
        try {
            $this->serviceUrl = (Setting::where('type', 'bitly_endpoint_url')->first())->value;
            $this->serviceKey = (Setting::where('type', 'bitly_endpoint_key')->first())->value;
            $this->serviceDomain = (Setting::where('type', 'bitly_endpoint_domain')->first())->value;
        } catch (\Throwable $th) {
            throw new Exception("QrCode Monkey: Ocorreu um erro ao tentar buscar a chave \n {$th}");
        }
    }

    public function generateShortUrl($longUrl)
    {

        $body = [
            "domain"   => $this->serviceDomain,
            "long_url" => $longUrl,
        ];

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->serviceKey,
        ];

        $reposnse = $this->dispatch($this->serviceUrl, 'POST', $headers, $body);

        return $reposnse->link?:null;
    }

}