<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserOperation;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function login(Request $request){
        
        $email = $request->email;
        $password = $request->password;

        if(Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();              
            
            $success['token'] = $user->createToken('login')->accessToken; 

           return response()->json([
               'response' => $success,
               'success'=>true,
               'user_data' => $user
           ], 200);
        }
        return response()->json([
            'error' => 'Unauthorized'
        ],401);
    }

    public function logout(){
        if(Auth::check()){
            Auth::user()->token()->revoke();
            return response()->json([
                'success' => 'Successfully logged out'
            ], 200);
        }
    }

    public function register(Request $request){
   
            $this->validate($request, [
                'name' => 'required|min:2',
                'email' => 'required|email|unique:users',
                //'password' => 'required|min:8',
                'password' => 'required',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $token = $user->createToken('register_token')->accessToken;
     
            return response()->json([
                'response' => $user,
                'success'=> true,
                'token' => $token
            ], 200);
        
        
        // register second method 
        /*$user = new User();
        $user->name = $request->name;
        $user->email =  $request->email;
        $user->password = Hash::make($request->password);
        $user->save();*/
    }

    public function getUserData(){
        $user = Auth::user();  
        return $user;
    }

    public function userOperationAdd(Request $request){
        
        $this->validate($request, [ 
            'total' => 'required',
            'category' => 'required',
            'currency' => 'required',
            'description' => 'required'
        ]);

        $operation = UserOperation::create([
            'total' => $request->total,
            'user_id' => Auth::user()->id,
            'category_id' => $request->category,
            'currency_id' => $request->currency,
            'description' => $request->description,
            'operation_date' => date('Y-m-d')
        ]);

        return response()->json([
            'success_message' => 'successfuly',
            'success' => $operation,
            //'token' => $token
        ], 200);

    }

    public function getUserOperation(){
      
        $userId = Auth::user()->id;

        $operations = UserOperation::with('user', 'category', 'currency')
                                    ->where('user_id', $userId)->get();
        return $operations;
    }

    public function totalFilter(){
        $userId = Auth::user()->id;
        $totalFilterOperations = UserOperation::with('user', 'category', 'currency')
                                              ->where('user_id', $userId)
                                              ->orderBy('total', 'desc')->get();
        return $totalFilterOperations;
    }   

    public function dateFilter(){
        $userId = Auth::user()->id;
        $dateFilterOperations = UserOperation::with('user', 'category', 'currency')
                                              ->where('user_id', $userId)
                                              ->orderBy('operation_date', 'desc')->get();
        return $dateFilterOperations;
    }

    public function getSingleUserOperation($id){
        //$userId = Auth::user()->id;
        $operation = UserOperation::with('user', 'category', 'currency')
                                ->where('user_id', 9)->where('id', $id)->get();
        return $operation;
    }

    public function operationUpdate(Request $request, $id){
       
        $this->validate($request, [ // validate ile gelen verilerde dogrulama yapiyoruz
            'total' => 'required',
            'category_id' => 'required',
            'currency_id' => 'required',
            'description' => 'required'
        ]);
        # TODO : React tarafında id yi get ile gonderme ile sorun cikarsa. normal verilerle beraber gonder,
                # bir alt satirda da $request->id diyerek ilgili veriye ulas.
        $operation = UserOperation::find($id);
        $operation->total =  $request->total;
        $operation->category_id = $request->category_id;
        $operation->currency_id = $request->currency_id;
        $operation->description = $request->description;
        
        $operation->save();

        return response()->json([
            'success_message' => 'Update operation successfuly',
        ], 200);
    }

    public function operationDelete($id){
        $operation = UserOperation::find($id);
        $operation->delete();
        return response()->json([
            'success_message' => 'Delete operation successfuly',
        ], 200);

    }

    /*public function allOperation(){
        $operations = UserOperation::all();
        return response()->json([
            'success_message' => 'All operations list successfuly',
            'data' => $operations,
        ], 200);

    }*/

    public function getRate(){ // anlik kur bilgisi
        $connect_web = simplexml_load_file('http://www.tcmb.gov.tr/kurlar/today.xml');
    
        $usd_buying = $connect_web->Currency[0]->BanknoteBuying;
        $usd_selling = $connect_web->Currency[0]->BanknoteSelling;
 
        $euro_buying = $connect_web->Currency[3]->BanknoteBuying;
        $euro_selling = $connect_web->Currency[3]->BanknoteSelling;
 
        return 'USD Alış: '.$usd_buying.'<br>USD Satış: '.$usd_selling.'<br>'.
                 'EUR Alış: '.$euro_buying.'<br>EUR Satış: '.$euro_selling;
        # TODO : KULLANI TARAFINDA BUNLARI LİSTELERKEN ANLİK KUR FİYATİYLA CARPİP LİSTELİCEZ
        
    }

    public function authenticate(Request $request){
        $user = [];
        if(Auth::check()){
            $user = $request->user();
        }
        return response()->json([
            'user'=>$user,
            'isLoggedIn'=>Auth::check()
        ]);
    }

    public function getCategory(){
        $categories = Category::all();
        return $categories;
    }

    public function getCurrency(){
        $currencies = Currency::all();
        return $currencies;
    }
    
    
    # TODO : SECİLEN İKİ TARİH ARASİNDA Kİ TUTARLARİN TRY CİNSİNDEN EKRANDA GORUNMESİ
}
