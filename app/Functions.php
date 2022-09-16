<?php

namespace App;

use Illuminate\Support\Facades\Response;



class Functions {
    public static function setResponse(array $resp, $code){
        return response()->json($resp,$code);
    }
}