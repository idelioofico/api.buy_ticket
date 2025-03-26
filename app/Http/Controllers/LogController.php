<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{

    public function save_log($state, $action, $description, $request_details, $response_details, $ip)
    {
        $log = new Log();
        $log->status = $state;
        $log->ip = $ip;
        $log->action = $action;
        $log->description = $description;
        $log->request = $request_details;
        $log->response = $response_details;
        $log->created_at =now(env('TIMEZONE'));
        $log->updated_at =now(env('TIMEZONE'));
        $log->save();
    }

}
