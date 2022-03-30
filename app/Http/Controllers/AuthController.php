<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


     class AuthController extends Controller
   {
      public function unauthorized()
   {
         return response()->json([
              'error' => 'Não autorizado'
         ], 401);
      }
       public function register(Request $request)
   {
      $array = ['error' => ''];
      $validator = Validator::make($request->all(), [
         'name' => 'required',
         'email' => 'required|email|unique:users,email',
         'matricular' => 'required|unique:users,matricular',
         'loja' => 'required',
         'password' => 'required'
     ]);
          if (!$validator->fails()) {
          $name = $request->input('name');
          $email = $request->input('email');
          $matricular = $request->input('matricular');
          $loja = $request->input('loja');
          $password = $request->input('password');
          $hash = password_hash($password, PASSWORD_DEFAULT);
          $newUser = new User();
          $newUser->name = $name;
          $newUser->email = $email;
          $newUser->matricular = $matricular;
          $newUser->loja = $loja;
          $newUser->password = $hash;
          $newUser->save();
          $token = auth()->attempt([
             'matricular' => $matricular,
             'password' => $password
   ]);
      if (!$token) {
           $array['error'] = 'Ocorreu um erro.';
        return $array;
      }
           $array['token'] = $token;

           $user = auth()->user();

          $array['user'] = $user;

      $properties = Unit::select(['id', 'name'])
      ->where('id_owner', $user['id'])
      ->get();

       $array['user']['properties'] = $properties;
    } else {
       $array['error'] = $validator->errors()->first();
    return $array;
   }
    return $array;
}
     public function login(Request $request){
     $array = ['error' => ''];

    $validator = Validator::make($request->all(), [
          'matricular' => 'required',
          'password' => 'required'
   ]);
    if (!$validator->fails()) {
        $matricular = $request->input('matricular');
        $password = $request->input('password');
        $token = auth()->attempt([
        'matricular' => $matricular,
        'password' => $password 
   ]);

    if (!$token) {
        $array['error'] = 'CPF e/ou senha estão errados.';
        return $array;
     }
      $array['token'] = $token;
      $user = auth()->user();
      $array['user'] = $user;
      $properties = Unit::query()->select(['id', 'name'])
      ->where('id_owner', $user['id'])
      ->get();
      $array['user']['properties'] = $properties;
      } else {
      $array['error'] = $validator->errors()->first();
    
      return $array;
}
return $array;
}
     public function validateToken(){
          
          $array = ['error' => ''];
          $user = auth()->user();
          $array['user'] = $user;
          $properties = Unit::query()->select(['id', 'name'])
          ->where('id_owner', $user['id'])
          ->get();
          $array['user']['properties'] = $properties;
          
          return $array;
        }

     public function logout()
    {
        $array = ['error' => ''];
        auth()->logout();
        return $array;
        }
    }