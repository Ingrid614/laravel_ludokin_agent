<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Functions;
use App\Models\Commande;


class CommissionController extends Controller
{
    public function create(Request $request) {
        try{
            DB::beginTransaction();
            $resp=['error'=>null,'data'=>null];
        
        $validated = Validator::make($request->all(),[
            'user_id'=> ['required','integer','max:255'],
            'commande_id'=> ['required','integer','max:255'],
            'taux'=> ['nullable'],
        ]);
        if($validated->fails()) {
         $resp['error']=$validated->errors();
         return Functions::setResponse($resp,500);
        }
        $data=$request->all();
        $montantCommande=Commande::where('id',$data['commande_id'])->first()->montant;
        $commission = Commission::create([
            'user_id' => $data['user_id'],
            'commande_id' => $data['commande_id'],
            'taux' =>$data['taux'],
            'montant_commission' => $montantCommande*$data['taux']*0.01,                                    
        ]);      
        $resp['data']=$commission;
        DB::commit(); 
    }catch(Exception $e){
        $resp['error']=$e->getMessage();
        DB::rollBack();
    }
    return $resp;
    }
     public function getAll(){
         return Commission::all();
     }

     public function get($id){
        $resp=['error'=>null,'data'=>null];
        $commission = Commission::find($id);
        if($commission==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }else{
            $resp['data']=$commission;
        }
        return $resp;
     }

     public function delete($id){
        $resp=['error'=>null,'data'=>null];
        $commission = Commission::find($id);
        if($commission==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }else{
            $commission->delete();
        }
     }

     public function update(Request $request, $id){
         try{
        DB::beginTransaction();
        $commission = Commission::find($id);
        $resp=['error'=>null,'data'=>null];
    
    $validated = Validator::make($request->all(),[
        'user_id'=> ['required','integer','max:255'],
        'commande_id'=> ['required','integer','max:255'],
        'taux'=> ['nullable']
    
    ]);
    if($validated->fails()) {
     $resp['error']=validated->errors();
     return Functions::setResponse($resp,500);
    }
        $data = $request->all();
        $montantCommande=Commande::where('id',$data['commande_id'])->first->montant;

        $commission->user_id = $data['user_id'];
        $commission->commande_id = $data['commande_id'];
        $commission->taux = $data['taux'];
        $commission->montant_commission =  $montantCommande*$data['taux']*0.01;
        
        $commission->save();

        $resp['data']=$commission;
         }
         catch(Exception $e){
            $resp['error']=$e->getMessage();
            DB::rollBack();
         }
         return $resp;
     }
}
