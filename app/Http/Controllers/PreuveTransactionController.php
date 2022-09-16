<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreuveTransaction;
use Illuminate\Support\Facades\DB;
use App\Functions;

class PreuveTransactionController extends Controller
{
    public function create(Request $request) {
        try{
        DB::beginTransaction();
        $resp=['error'=>null,'data'=>null];
        $validated = Validator::make($request->all(),[
            'email_client'=> ['required','string','email','max:255','unique:users'],
            'nom_client'=> ['required','string','max:255'],
        
        ]);
        if($validated->fails()) {
            $resp['error']=$validated->errors();
            return Functions::setResponse($resp,500);
        }
        $data=$request->all();
        $preuve = PreuveTransaction::create([
            'url' => $data['url'],
            'commande_id' => $data['commande_id']                            
        ]);
        $resp['data']=$preuve;
        DB::commit();
        }
        catch(Exception $e){
        $resp['error']=$e->getMessage();
        DB::rollBack();
        }
        return $resp ;
    }
     public function getAll(){
         return PreuveTransaction::all();
     }

     public function get($id){
        $resp=['error'=>null,'data'=>null];
        $preuve= PreuveTransaction::find($id);
        if($preuve==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }
        else{
            $resp['data']=$preuve;
        }
        return $resp;
     }

     public function delete($id){
        $resp=['error'=>null,'data'=>null];
        $preuve= PreuveTransaction::find($id);
        if($preuve==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }
        else{
            $preuve->delete();
        }
     }

     public function update(Request $request, $id){
        try{
        DB::beginTransaction();
        $resp=['error'=>null,'data'=>null];
        $preuveTransaction = PreuveTransaction::find($id);
        $validated = Validator::make($request->all(),[
            'email_client'=> ['required','string','email','max:255','unique:users'],
            'nom_client'=> ['required','string','max:255'],
        
        ]);
        if($validated->fails()) {
            $resp['error']=$validated->errors();
            return Functions::setResponse($resp,500);
        }
        $data = $request->all();

        $preuveTransaction->url = $data['url'];
        $preuveTransaction->id_commande = $data['id_commande'];
      
        $preuveTransaction->save();
        $resp['data']=$preuveTransaction;
        DB::commit();
        }
         catch(Exception $e){
            $resp['error'] = $e->getMessage();
            DB::rollBack();
        }
        return $resp;
     }
}
