<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Logs;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{


    public function checkout_link(Request $request)
    {

        $response = [];
        $status = 503;

        try {
            // DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'ticket' => 'required|exists:tickets,id',
                'qty' => 'required|gt:0',
                'user' => 'required',
                'payment_method' => 'required',
                'payment_msisdn' => 'required_if:payment_method,mobile_wallet',
            ]);

            if ($validator->fails()) {

                $response = array(
                    'status' => $status = 412,
                    'success' => false,
                    'data' => $validator->errors(),
                    'message' => 'A validação dos campos falhou, porfavor, verifique e tente novamente',
                );
            } else {


                $ticket = Ticket::find($request->ticket);
                if (!empty($ticket)) {

                    if ($request->payment_method == "mobile_wallet") {

                        $attributes = array(
                            'amount' => $request->qty * $ticket->price,
                            'type' => 'c2b',
                            'msisdn' => Helper::validateMSISDN($request->payment_msisdn),
                            'payment_method' => $wallet = Helper::IdentifyPrefixByWalletId($request->payment_msisdn)
                        );

                        $walletTransactionResponse = Helper::paymentMethod($wallet)->c2b($attributes);
                        if (!empty($walletTransactionResponse) && $walletTransactionResponse['success']) {

                            $customer = null;
                            $found_customer = Customer::where('mobile_number', $request->user['phone_number'])->first();

                            if (empty($found_customer)) {

                                $customer = new Customer();
                                $customer->name = $request->user['pushname'];
                                $customer->bot_id = $request->user['_id']['$oid'];
                                $customer->mobile_number = $request->user['phone_number'];
                                $customer->created_at = now();
                                $customer->updated_at = now();
                                $customer->save();

                            } else {

                                $customer = $found_customer;

                            }

                            $discount = 0;
                            $order = new Order();
                            $order->code = strtotime(now());
                            $order->event_id = $ticket->event_id;
                            $order->customer_id = $customer->id;
                            $order->subtotal = $subtotal = doubleval($ticket->price) * intval($request->qty);
                            $order->total = $total = $subtotal - $discount;
                            $order->type = 'compra';
                            $order->ticket_id = $ticket->id;
                            $order->created_at = now();
                            $order->updated_at = now();
                            $order->save();

                            $orderDetail = new OrderDetail();
                            $orderDetail->order_id = $order->id;
                            $orderDetail->code = $code = $this->generateTicketCode($ticket->id);
                            $orderDetail->ticket_id = $ticket->id;
                            $orderDetail->price = $ticket->price;
                            $orderDetail->qty = $request->qty;
                            $orderDetail->qr_code = $this->generateQrCode($code);
                            $orderDetail->due_date = $ticket->event->end_date;
                            $orderDetail->status = 'pendente';
                            $orderDetail->created_at = now();
                            $orderDetail->updated_at = now();
                            $orderDetail->save();

                            $payment = new Payment();
                            $payment->order_id = $order->id;
                            $payment->amount = $total;
                            $payment->payment_method = $wallet;
                            $payment->status = 'completed';
                            $payment->created_at = now();
                            $payment->updated_at = now();
                            $payment->save();

                            // $paymentLink=new PaymentLink();
                            // $paymentLink->order_id=$order->id;
                            // $payment->link="";

                            $response = array(
                                'status' => $status = 201,
                                'success' => true,
                                'data' => $orderDetail,
                                'message' => 'Pagamento efectuado com sucesso',
                            );

                            // dd("Hello:", $payment);
                        } else {

                            $response = array(
                                'status' => $status = 503,
                                'success' => false,
                                'data' => [],
                                'message' => 'Oops, tivemos um problema ao tentar efectivar o pagamento. Porfavor, tente novamente ou contacte a equipa de suporte',
                            );
                        }

                }else{





                }

            } else {

                $response = array(
                    'status' => $status = 404,
                    'success' => false,
                    'data' => [],
                    'message' => 'Ticket não encontrado.',
                );
            }
            }
        } catch (\Exception $ex) {

            $response = array(
                'status' => $status = 503,
                'success' => false,
                'data' => [],
                'message' => 'Oops, ocorreu um erro ao tentar efectuar pagamento, porfavor contacte a equipa de suporte.',
                'exception'=>json_encode($ex->getMessage()),
            );
        }

        Logs::create(
            [
                'action' => 'mobile_wallet_payment',
                'request' => json_encode($request->all()),
                'response' => json_encode($response),
                'ip' => $request->ip(),
                'user' => ''
            ]
        );

        return response()->json($response, $status);
    }



    private function generateTicketCode($ticketId)
    {
        return 'TICKET-' . $ticketId . '-' . \Str::random(6);
    }

    private function generateQrCode($ticketCode)
    {
        // Gera o QR code com o código do ticket
        $qrCode = QrCode::size(150)->generate($ticketCode);

        $filename = 'qrcodes/' . $ticketCode . '.svg';
        // Salva o QR code em um arquivo (opcional)
        $qrCodePath = public_path($filename);
        file_put_contents($qrCodePath, $qrCode);

        return  $filename; // Retorna o caminho do QR code
    }
}
