<?php

namespace App\Http\Controllers;

use App\Functions;
use App\Models\Statut;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatutController extends Controller
{
    public function create(Request $request){
        try{
        DB::beginTransaction();
        $resp=['error'=>null,'data'=>null];
        $validated = Validator::make($request->all(),[
            'statut'=> ['required','string'],
        ]);
        if($validated->fails()) {
            $resp['error']=$validated->errors();
            return Functions::setResponse($resp,500);
        }
        $data=$request->all();
        $statut = Statut::create([
            'statut' => $data['statut']
        ]);
        $resp['data']=$statut;
        DB::commit();
        }
        catch(Exception $e){
        $resp['error']=$e->getMessage();
        DB::rollBack();
        }
        return $resp;
    }

    public function getAll(){
        return Statut::all();
    }

    public function get($id){
        $resp=['error'=>null,'data'=>null];
        $statut= Statut::find($id);
        if($statut==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }
        else{
            $resp['data']=$statut;
        }
        return $resp;
    }

    public function delete($id){
        $resp=['error'=>null,'data'=>null];
        $statut= Statut::find($id);
        if($statut==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }
        else{
            $statut->delete();
        }
     }

    public function update($request,$id){
        try{
        DB::beginTransaction();
        $statut=Statut::find($id);
        $resp=['error'=>null,'data'=>null];

        $validated = Validator::make($request->all(),[
            'statut'=> ['required','string'],
        ]);
        if($validated->fails()) {
            $resp['error']=$validated->errors();
            return Functions::setResponse($resp,500);
        }

        $data = $request ->all();
        $statut=Statut::find($id);
        
        $statut->statut = $data['statut'];
        $statut->save();
        $resp['data']=$statut;
        DB::commit();
        }
        catch(Exception $e){
        $resp['error']=$e->getMessage();
        DB::rollBack();
        }
        return $resp;
    }
}
