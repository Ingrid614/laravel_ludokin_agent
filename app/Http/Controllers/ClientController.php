<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use Brick\Math\BigInteger;
use App\Functions;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\Stub\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
   
    public function create(Request $request) {
        try{
            DB::beginTransaction();
        $resp=['error'=>null,'data'=>null];
        $validated = Validator::make($request->all(),[
            'adresse_kin' =>['required','string'],
            'email_client'=> ['required','string','email','unique:clients'],
            'nom_client'=> ['required','string','max:255'],
            'numero_whatsapp' =>['required','string',],
           
        
        ]);
        if($validated->fails()){
            $resp['error']=$validated->errors();
            return Functions::setResponse($resp,500);
        }

        $data=$request->all();
        $client= Client::create([
            'adresse_kin' => $data['adresse_kin'],
            'numero_whatsapp' => $data['numero_whatsapp'],
            'email_client' =>$data['email_client'],
            'nom_client' => $data['nom_client'],
                                              
        ]);
        $resp['data']=$client;
        DB::commit();  
        }
        catch(Exception $e){
            $resp['error'] = $e->getMessage();
            DB::rollBack();
        }
        return $resp;
    }
     public function getAll(){
         return Client::all();
     }

     public function get($id){
        $resp=['error'=>null,'data'=>null];
         $client = Client::find($id);
     
        if($client==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }else{
            $resp['data']=$client;
        }

        return $resp;
     }

     public function delete($id){
        $resp=['error'=>null,'data'=>null];
        $client = Client::find($id);
    
       if($client==null){
           $resp['error']='Not found';
           return Functions::setResponse($resp,404);
       }else{
           $client->delete();
       }
     }

     public function update(Request $request, $id){
        try{
        DB::beginTransaction();
        $resp=['error'=>null,'data'=>null];
        $client = Client::find($id);

        $validated = Validator::make($request->all(),[
            'email_client'=> ['required','string','email','max:255',Rule::unique('clients')->ignore($client->id)],
            'nom_client'=> ['required','string','max:255'],
            'numero_whatsapp' =>['required','string',],
            'nom_client' =>['required','string']
        
        ]);

        if($validated->fails()){
            $resp['error'] = $validated->errors();
            return Functions::setResponse($resp, 500);
        }
        $data = $request->all();

        $client->adresse_kin = $data['adresse_kin'];
        $client->numero_whatsapp = $data['numero_whatsapp'];
        $client->email_client = $data['email_client'];
        $client->nom_client = $data['nom_client'];
        
        $client->save();

        $resp['data']=$client;
        DB::commit();
    }
    catch(Exception $e){
        $resp['error']=$e->getMessage();
        DB::rollBack();
    }
    return $resp;
     }

}
