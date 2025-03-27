<?php

namespace App\Services;

use App\DTO\TabluPayDTO;
use App\Helpers\Helper;
use App\Interfaces\IPaymentGateway;
use App\Models\Configuration;
use App\Models\Notification;
use App\Repositories\WalletTransactionRepository;
use Illuminate\Support\Facades\App;

class EMolaPaymentGateway implements IPaymentGateway
{
    protected $walletTransactionRepository;
    private $validPrefixes="67";

    public function __construct(WalletTransactionRepository $walletTransactionRepository)
    {
        $this->walletTransactionRepository = $walletTransactionRepository;
    }

    public function c2b($attributes)
    {
        $paymentResponse = null;

        $msisdn = str_replace("258", '', Helper::validateMSISDN($attributes['msisdn'],$this->validPrefixes));
        $reference = Helper::referenceGenerator();
        $attributes['reference'] = $reference;

        $request = [
            'amount' => $amount = (float)($attributes['amount']),
            'content' => $content = $this->contentGenerator($amount, $reference),
            'business' => Configuration::getSingleValue('service_c2b_emola'),
            'reference' => $reference,
            'msisdn' => $msisdn
        ];

        $attributes['request'] = json_encode($request);
        $walletTransactionLog = $this->walletTransactionRepository->store($attributes);
        $eMolaResponse = (App::make(TabluPayDTO::class))->submit_payment($request);

        $attributes['response'] = json_encode($eMolaResponse);
        if (!empty($eMolaResponse) && isset($eMolaResponse->success) && $eMolaResponse->success == true) {

            $walletTransactionLog->success = true;

            $paymentResponse = array(
                'success' => true,
                'data' => $walletTransactionLog,
                'message' => 'Pagamento realizado com sucesso.'
            );

        } else {
            $walletTransactionLog->success = false;
            $paymentResponse = array(
                'success' => false,
                'data' => null,
                'message' => $eMolaResponse->message
            );
        }

        $walletTransactionLog->response = json_encode($eMolaResponse);
        $walletTransactionLog->save();

        return $paymentResponse;
    }

    public function contentGenerator($amount, $reference,$operation="transacao")
    {
        $amount = number_format($amount, 2, ',', '.');
        $operation=$operation;
        $content = Configuration::getSingleValue('emola_trans_content');
        $content=str_replace('{amount}', $amount, $content);
        $content=str_replace('{reference}', $reference, $content);
        $content=str_replace('{operation}', $operation, $content);

        return $content;
    }
}
