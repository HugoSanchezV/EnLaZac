<?php

namespace App\Services;

use Illuminate\Http\Request;

class RequestService
{
    public static function getArrayIndexRequest(Request $request = null)
    {
        return [
            'q' => $request["q"] ?? "",
            'type' => $request["type"] ?? "",
            'order' => $request["order"] ?? "",
            'attribute' => $request["attribute"] ?? "",
        ];
    }
}
