<?php

namespace App\Traits;

trait ResponseTrait
{
    protected function successResponse($data = null, $message = null, $code = 200, $token = null)
    {
        return response()->json([
            'status'     => 'Success',
            'message'    => $message,
            'model'      => $data,
            'error_list' => [],
            'code'       => $code
        ], $code, $token ? ['Authorization' => $token] : []);
    }

    protected function errorResponse($message = null, $code = 500, $errorList = [], $data = null)
    {
        return response()->json([
            'status'     => 'Error',
            'message'    => $message,
            'model'      => $data,
            'error_list' => $errorList,
            'code'       => $code
        ], $code);
    }
}
