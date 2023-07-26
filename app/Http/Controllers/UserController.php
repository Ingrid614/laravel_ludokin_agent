<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Functions;
use App\Models\Commande;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $resp = ['error' => null, 'data' => null , 'id' => null];
            $validated = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string'],
                'numero_CNI' => 'nullable|present|unique:users',
                'date_de_naissance' => ['required','date_format:Y-m-d'],
                'localisation' => 'present|string',
                'user_code' => 'present|string|unique:users',
                'parent_code' => 'nullable|string'
            ]);
            if ($validated->fails()) {
                $resp['error'] = $validated->errors();
                return Functions::setResponse($resp, 500);
            }

            $data = $request->all();
       
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'numero_CNI' => $data['numero_CNI'] ?? "",
                'date_de_naissance' => $data['date_de_naissance'],
                'numero_commercial' => $data['numero_commercial'] ?? "",
                'localisation' => $data['localisation'],
                'user_code' => $data['user_code'],
                'parent_code' => $data['parent_code']
            ]);
            DB::commit();
            $resp['data'] = $user;
            $resp['id'] = $user->id;

            return $resp;
        } catch (Exception $e) {
            $resp['error'] = $e->getMessage();
            DB::rollBack();
        }
        return $resp;
    }
    public function getAll()
    {
        return User::all();
    }

    public function get($id)
    {
        $resp=['error'=>null,'data'=>null];
        $user= User::find($id);
        if($user==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }
        else{
            $resp['data']=$user;
        }
        return $resp;
    }

    public function delete($id)
    {   $resp=['error'=>null,'data'=>null];
        $user= User::find($id);
        if($user==null){
            $resp['error']='Not found';
            return Functions::setResponse($resp,404);
        }
        else{
            $user->delete();
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            $resp = ['error' => null, 'data' => null, 'id' => null];
            $validated = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'numero_CNI' => 'nullable|present',
                'numero_commercial' => 'nullable|present',
                'date_de_naissance' => ['required','date_format:Y-m-d'],
                'localisation' => 'nullable|present|string'
            ]);
            if ($validated->fails()) {
                $resp['error'] = $validated->errors();
                return Functions::setResponse($resp, 500);
            }

            $data = $request->all();

            $user->name = $data['name'];
            $user->numero_CNI = $data['numero_CNI'];
            $user->date_de_naissance = $data['date_de_naissance'];
            $user->numero_commercial = $data['numero_commercial'];
            $user->localisation = $data['localisation'];

            $user->save();
            $resp['data']=$user;
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return $resp;
    }

    public function login(Request $request)
    {
        DB::beginTransaction();
        try {
            $resp = ['error' => null, 'data' => null];
            $data = $request->all();
            $validated=Validator::make($request->all(),[
                'email'=> 'required|string',
                'password'=>'required|string'
            ]);

            if($validated->fails()){
                $resp['error'] = $validated->errors();
                return Functions::setResponse($resp,500);
            }


            $user = User::where('email', $data['email'])->get()->shift();

            if ($user) {
                $user->makeVisible(['password']);
                $password = $user->password;
                if (Hash::check($data['password'], $password)) {
                    $user->makeHidden(['password']);
                    $resp['data'] = $user;
                    $resp['id'] = $user->id;
                    return Functions::setResponse($resp, 200);
                } else {
                    $resp['error'] = 'wrong credentials';
                    return Functions::setResponse($resp, 401);
                }
            } else {
                $resp['error'] = 'no user found';
                return Functions::setResponse($resp, 401);
            }
           
        } catch (Exception $e) {
            $resp['error'] = $e->getMessage();
            DB::rollBack();
            dd($e->getMessage());
            return $resp;
        }
    }

    public function getCommandUser($id){
        $resp = ['data'=>null, 'error'=>null];
        $user = User::find($id);
        if(!$user){
            $resp['error'] = "No user found";
            return Functions::setResponse($resp,401);
        }
        $command = Commande::whereUserId($id)->latest()->get();
        $resp['data'] = $command;
        return Functions::setResponse($resp,200);
    }

}
