<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BaseService
{
    protected $user;

    function __construct($user=null)
    {
        if($user){
            $this->user=$user;
        }else{
            $this->user=Auth::user();
        }
    }

    static function safeUser($guard='user'){
        try{
            $token=request()->header('Authorization');
            if($token){
                if(is_numeric($token) && config('app.debug')){
                    return auth($guard)->setToken(auth($guard)->tokenById($token))->user();
                }
                return auth($guard)->user();
            }

        }catch (\Exception $e){
        }
        return null;
    }
}
