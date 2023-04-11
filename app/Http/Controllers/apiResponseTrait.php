<?php

namespace App\Http\Controllers;

trait apiResponseTrait
{

    public function apiResponse($message = null, $meta = null, $items = null, $error = null, $code = 200)
    {

        $array = [
            'meta'    => $meta,
            'items'   => $items,
            'status'  => in_array($code, $this->successCode()) ? true : false,
            'message' => $message,
            'errors'  => $error,
        ];

        return response($array, $code);
    }

    public function successCode()
    {
        return [
            200, 201, 202,
        ];
    }
}
