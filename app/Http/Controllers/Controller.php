<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($message = '', $result = [], $statusCode = 200)
    {

        session()->flash('message', $message);
        $message = session()->get('message');


        return response()->json([
            'success' => true,
            'message' => $message,
            ...$result


        ], $statusCode);
    }
    public function sendError($error, $statusCode = 500)
    {
        session()->flash('errors', $error);
        $errors = session()->get('errors');
        return response()->json([
            'success' => false,
            'errors' => ['error' => $errors]
        ], $statusCode);
    }
}
