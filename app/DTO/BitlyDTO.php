<?php

namespace App\DTO;

use App\Models\Setting;
use Exception;

class BitlyDTO extends BaseDTO
{

    protected $serviceUrl;
    protected $serviceKey;
    protected $serviceDomain;

    public function __construct()
    {
        try {
            $this->serviceUrl = (Setting::where('type', 'qrcode_endpoint_url')->first())->value;
            $this->serviceKey = (Setting::where('type', 'qrcode_endpoint_key')->first())->value;
            $this->serviceDomain = (Setting::where('type', 'qrcode_endpoint_domain')->first())->value;
        } catch (\Throwable $th) {
            throw new Exception("Bitly: Ocorreu um erro ao tentar buscar a chave \n {$th}");
        }
    }

   
   /**
    * It takes a long URL and returns a short URL
    * 
    * @param longUrl The long URL to be shortened.
    * @param title The title of the link.
    * 
    * @return The response is an object with a link property.
    */
    public function generateShortUrl($longUrl, $title = '')
    {

        $body = [
            "domain"   => $this->serviceDomain,
            "long_url" => $longUrl,
        ];

        if (isset($title) && !empty($title)) {
            $body["title"] = $title;
        }

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->serviceKey,
        ];

        $reposnse = $this->dispatch($this->serviceUrl, 'POST', $headers, $body);

        return $reposnse->link ?: null;
    }
}
