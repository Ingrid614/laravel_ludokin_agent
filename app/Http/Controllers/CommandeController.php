<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use App\Functions;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Commission;
use App\Models\Settings;
use App\Models\User;
use Exception;

class CommandeController extends Controller
{

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $resp = ['error' => null, 'data' => null , 'command' => null];
            $validated = Validator::make($request->all(), [
                'user_id' => ['required', 'integer'],
                'adresse_kin' =>['required','string'],
                'montant' => ['required', 'numeric'],
                'statut_id' => ['integer']
            ]);
            
            if ($validated->fails()) {
                $resp['error'] = $validated->errors();
                return Functions::setResponse($resp, 500);
            }

            
            $data = $request->all();
            $montant_cout = $data['montant'] * Settings::find(1)->taux_cout * 0.01;
            $montantCommission = $data['montant'] * Settings::find(1)->taux_commission * 0.01;

            $client = Client::where('adresse_kin',$data['adresse_kin'])->first();
            if(!$client){
                $validated = Validator::make($request->all(),[
                    'adresse_kin' =>['required','string'],
                    'email_client'=> ['required','string','email'],
                    'nom_client'=> ['required','string','max:255'],
                    'numero_whatsapp' =>['required','string',],
                ]);
                if($validated->fails()){
                    $resp['error']=$validated->errors();
                    return Functions::setResponse($resp,500);
                }
                $client= Client::create([
                    'adresse_kin' => $data['adresse_kin'],
                    'numero_whatsapp' => $data['numero_whatsapp'],
                    'email_client' =>$data['email_client'],
                    'nom_client' => $data['nom_client'],
                ]);
            }
            $command = Commande::create([
                'user_id' => $data['user_id'],
                'client_id' => $client['id'],
                'adresse_kin' => $data['adresse_kin'],
                'montant' => $data['montant'],
                'taux' => Settings::find(1)->taux_cout,
                'cout' => $montant_cout,
                'statut_id' => $data['statut_id']
            ]);

            $commission = Commission::create([
                'user_id' => $data['user_id'],
                'commande_id' => $command->id,
                'taux' => Settings::find(1)->taux_commission,
                'montant_commission' => $montantCommission,
            ]);
            $resp['data'] = ['command' => $command, 'commision' => $commission, 'client'=> $client];
            $resp['command'] = $command;
            DB::commit();
        } catch (Exception $e) {
            // dd($e->getMessage());
            $resp['error'] = $e->getMessage();
            DB::rollBack();
        }
        return $resp;
    }
    public function getAll()
    {
        return Commande::all();
    }

    public function get($id)
    {
        $resp = ['error' => null, 'data' => null];
        $commande = Commande::find($id);
        if ($commande == null) {
            $resp['error'] = 'Not found';
            return Functions::setResponse($resp, 404);
        } else {
            $resp['data'] = $commande;
        }
        return $resp;
    }

    public function delete($id)
    {
        $resp = ['error' => null, 'data' => null];
        $commande = Commande::find($id);
        if ($commande == null) {
            $resp['error'] = 'Not found';
            return Functions::setResponse($resp, 404);
        } else {
            $commande->delete();
        }

    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $resp = ['error' => null, 'data' => null];
            $commande = Commande::find($id);
            $validated = Validator::make($request->all(), [
                'user_id' => ['required', 'integer'],
                'client_id' => ['required', 'integer'],
                'adresse_kin' => ['required', 'string'],
                'montant' => ['required', 'numeric'],
                'taux' => 'nullable',
                'statut_id' => ['integer']
            ]);
            if ($validated->fails()) {
                $resp['error'] = $validated->errors();
                return Functions::setResponse($resp, 500);
            }
            $data = $request->all();

            $commande->user_id = $data['user_id'];
            $commande->client_id = $data['client_id'];
            $commande->montant = $data['montant'];
            $commande->taux = $data['taux'];
            $commande->cout = $data['montant'] * $data['taux'] * 0.01;
            $commande->statut_id = $data['statut_id'];

            $commande->save();
            $resp['data'] = $commande;
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return $resp;
    }
}