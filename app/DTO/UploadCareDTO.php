<?php

namespace App\DTO;

use App\DTO\BaseDTO;
use App\Models\Setting;
use CURLFile;
use Exception;

class UploadCareDTO extends BaseDTO{


    protected $serviceUrl;
    protected $serviceKey;
    protected $serviceDomain;
    protected $cdn;

    public function __construct()
    {
      
        try {
            $this->serviceUrl = (Setting::where('type', 'uploadcare_endpoint_url')->first())->value;
            $this->serviceKey = (Setting::where('type', 'uploadcare_endpoint_key')->first())->value;
            // $this->serviceDomain = (Setting::where('type', 'uploadcare_endpoint_domain')->first())->value;
            $this->cdn = (Setting::where('type', 'uploadcare_endpoint_cdn')->first())->value;
        } catch (\Throwable $th) {
            throw new Exception("Uploadcare: Ocorreu um erro ao tentar buscar a chave \n {$th}");
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
    public function upload($path)
    {

        $body = [
            "UPLOADCARE_PUB_KEY"=> $this->serviceKey,
            "UPLOADCARE_STORE" => 1,//permanent,
            "filename"=>new CURLFile($path)
        ];

        $headers = "";

        $reposnse = $this->dispatch($this->serviceUrl, 'POST', $headers, $body);

        return "{$this->cdn}"."{$reposnse->filename}" ?: null;
    }

}