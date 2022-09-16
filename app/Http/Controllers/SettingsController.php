<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use Illuminate\Support\Facades\Validator;
use App\Functions;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function updateSettings(Request $request){
        try{
        DB::beginTransaction();
        $resp=['error'=>null,'data'=>null];
        $setting=Settings::find(1);
        $validated = Validator::make($request->all(),[
            'taux_cout'=>['nullable','numeric'],
            'taux_commission'=>['nullable','numeric']
        ]);
        if($validated->fails()){
            $resp['error']=$validated->errors();
            return Functions::setResponse($resp,500);
        }
        $data = $request->all();
        if($data['taux_commission']==null){
        $setting->taux_cout = $data['taux_cout'];
        }
        if($data['taux_cout']==null){
            $setting->taux_commission = $data['taux_commission'];
        }
        $setting->save();

        $resp['data']=$setting;
        DB::commit();
    }
    catch(Exception $e){
        $resp['error']=$e->gatMessage();
        DB::rollBack();
    }
    return $resp;
    }
    
}
