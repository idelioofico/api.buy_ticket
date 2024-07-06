<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Logs;
use Illuminate\Http\Request;

class CustomerControllerApi extends Controller
{
    /**
     * Display the specified resource.
     *s
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function consult(Request $request)
    {
        $response = array('data' => [], 'status' => $status = 404);
        $customer = Customer::where([['mobile_number', 'like', $request->mobile_number]])->first();

        if (!empty($customer)) {

            $response = array('data' => $customer, 'status' => $status = 200);

        } else {

           $new_customer= Customer::create([
                'firstname' => "",
                'mobile_number' => $request->mobile_number
            ]);

            Logs::create(
                [
                    'action' => 'store_customer',
                    'request' => json_encode($request->all()),
                    'response' => json_encode($new_customer),
                    'ip' => $request->ip(),
                    'user' => $request->mobile_number
                ]
            );

            $response = array('data' => $customer, 'status' => $status = 202);
        }

        Logs::create(
            [
                'action' => 'get_customer',
                'request' => json_encode($request->all()),
                'response' => json_encode($response),
                'ip' => $request->ip(),
                'user' => $request->mobile_number
            ]
        );
        return response()->json($response, $status);
    }
}
