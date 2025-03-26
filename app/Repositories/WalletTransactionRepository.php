<?php

namespace App\Repositories;

use App\Helpers\ArrayHelper;
use App\Helpers\Helper;
use App\Models\Transaction;
use App\Models\WalletTransaction;

class WalletTransactionRepository extends BaseRepository
{

    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        parent::__construct($transaction);
    }

    public function store($attributes,$wt_id="",$response="")
    {

        $data = array(
            'amount' => ArrayHelper::ValidateKeyData('amount', $attributes),
            'type' => ArrayHelper::ValidateKeyData('type', $attributes),
            'msisdn'=>Helper::validateMSISDN(ArrayHelper::ValidateKeyData('msisdn', $attributes)),
            'payment_method'=>ArrayHelper::ValidateKeyData('payment_method', $attributes),
            'reference' => ArrayHelper::ValidateKeyData('reference', $attributes),
            'request'=> ArrayHelper::ValidateKeyData('request', $attributes),
            'response' => $response?:ArrayHelper::ValidateKeyData('response', $attributes),
            'created_at'=>now(env('TIMEZONE')),
            'updated_at'=>now(env('TIMEZONE')),
        );

        return $this->transaction->create($data);
    }
}
