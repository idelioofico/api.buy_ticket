<?php


namespace App\Services;

use App\DTO\TabluPayDTO;
use App\Helpers\Helper;
use App\Interfaces\IPaymentGateway;
use App\Models\Configuration;
use App\Repositories\WalletTransactionRepository;
use Illuminate\Support\Facades\App;

class MpesaPaymentGateway implements IPaymentGateway
{

    protected $walletTransactionRepository;


    public function __construct(WalletTransactionRepository $walletTransactionRepository)
    {
        $this->walletTransactionRepository=$walletTransactionRepository;
    }

    public function c2b($attributes)
    {
       $paymentResponse = null;


        $request=[
            'msisdn'=>Helper::validateMSISDN($attributes['msisdn']),
            'amount'=>$attributes['amount'],
            'business'=>Configuration::getSingleValue('service_c2b_mpesa'),
            'reference'=>Helper::referenceGenerator()
        ];

        $attributes['request']=json_encode($request);
        $payment = $this->walletTransactionRepository->store($attributes);

        $mpesaResponse = App::make(TabluPayDTO::class)->submit_payment($request);
        $attributes['response'] = json_encode($mpesaResponse->data);
        $rawResponse = json_encode($mpesaResponse);


        if (!empty($mpesaResponse) && $mpesaResponse->success  &&  $mpesaResponse->data->output_ResponseCode == "INS-0") {

            $payment->wt_success=true;
            $paymentResponse = array(
                'success' => true,
                'data' => $payment,
                'message' => 'Pagamento realizado com sucesso.'
            );

        } else {

            $payment->success=false;
            $paymentResponse = array(
                'success' => false,
                'data' => $mpesaResponse,
                'message' => $mpesaResponse->data,
                'error'=> $rawResponse,
            );
        }
        $payment->reference=$mpesaResponse->data->output_ThirdPartyReference;
        $payment->response=$attributes['response'];
        $payment->created_at = now(env('TIMEZONE'));
        $payment->save();

        return $paymentResponse;
    }

}
