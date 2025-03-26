<?php

namespace App\Helpers;

use App\Services\EMolaPaymentGateway;
use App\Services\MoneyPaymentGateway;
use App\Services\MpesaPaymentGateway;
use Exception;
use Illuminate\Support\Facades\App;

class Helper
{

public static function paymentMethod($method)
    {
        $paymentMethod = null;

        switch ($method) {
            case '1':
                $paymentMethod = App::make(MpesaPaymentGateway::class);
                break;
            case '2':
                $paymentMethod = App::make(EMolaPaymentGateway::class);
                break;
            default:
                throw new Exception("Metodo de pagamento invalido");
        }
        return $paymentMethod;
    }


    public static function referenceGenerator($size = 10)
    {
        $codigo = "";
        $tamanho = $size;
        $chars = "122346789ABCDEFGHIKJKMLNOPQRSTUVWYXZ987";

        $total_l = strlen($chars) - 1;
        for ($i = 0; $i < $tamanho; $i++) {
            $posicao = rand(0, $total_l);
            $codigo = $codigo . $chars[$posicao];
        }

        return $codigo;
    }

    public static function IdentifyPrefixByWalletId($msisdn)
    {
        $prefix = "";

        if (strlen($msisdn) > 9) {
            $prefix = substr($msisdn, 3, 2);
        } else {
            $prefix = substr($msisdn, 0, 2);
        }

        $prefixToWalletId = [
            '84' => 1,
            '85' => 1,
            '86' => 2,
            '87' => 2,
        ];

        return isset($prefixToWalletId[$prefix]) ? $prefixToWalletId[$prefix] : null;
    }

    public static function IdentifyMobileWalletId($msisdn)
    {
        $prefix = "";

        if (strlen($msisdn) > 9) {
            $prefix = substr($msisdn, 3, 2);
        } else {
            $prefix = substr($msisdn, 0, 2);
        }

        $prefixToWalletId = [
            '84' => 1,
            '85' => 1,
            '86' => 2,
            '87' => 2,
        ];

        return isset($prefixToWalletId[$prefix]) ? $prefixToWalletId[$prefix] : null;
    }

    public static function getMsisdnPrefix($msisdn){

$prefix = "";

if (strlen($msisdn) > 9) {
    $prefix = substr($msisdn, 3, 2);
} else {
    $prefix = substr($msisdn, 0, 2);
}
return $prefix;
}


public static function ValidateMSISDN($msisdn, $prefix = "234567")
    {
        $validMSISDN = "";
        $prefix = $prefix ?: "234567";

        // Define patterns to match different formats
        $patterns = [
            "/^8[" . $prefix . "][0-9]{7}$/",
            "/^258(8[" . $prefix . "][0-9]{7})$/",
            "/^\+258(8[" . $prefix . "][0-9]{7})$/",
            "/^84 [0-9]{3} [0-9]{4}$/"
        ];

        // Check each pattern
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $msisdn, $matches)) {
                if ($pattern === $patterns[0]) {
                    // For local numbers without country code
                    $validMSISDN = "258" . $msisdn;
                } elseif ($pattern === $patterns[3]) {
                    // For numbers with spaces
                    $validMSISDN = "258" . str_replace(' ', '', $msisdn);
                } else {
                    // For numbers already including the country code
                    $validMSISDN = preg_replace('/^\+?258/', '258', $msisdn);
                }
                return $validMSISDN;
            }
        }

        // Return false if no pattern matches
        return false;
    }
}
