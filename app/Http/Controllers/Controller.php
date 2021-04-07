<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function send($result, $message, $code = 200, $redirect = "")
    {
        $response = [
            'result'    => $result,
            'message' => $message,
            'success' => true,
            'status_code' => $code,
        ];
        if ($redirect) {
            $response['redirect'] = $redirect;
        }
        return response()->json($response, $code);
    }

}
