<?php

namespace CareSet\CareSetJWTAuthClient\Controller;

use Illuminate\Http\Request;

use \Firebase\JWT\JWT;
use \Exception;
use \UnexpectedValueException;
use \Firebase\JWT\BeforeValidException;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Controller;

class CareSetJWTLoginController extends Controller
{
    public function callback(Request $request)
    {
    	$callback = $request->callback;
    	$return = $request->return;
    	$token = $request->token;

    	$user = JWT::decode($token,config('caresetjwtclient.public_key'),config('caresetjwtclient.algo'));

    	$User = User::where('email',$user->email)->first();

    	if(!$User)
    	{
			$User = User::Create([
		        	'name'=>$user->name,
		        	'email'=>$user->email,
		        	'last_token'=>$token,
		        	'is_admin'=>$user->is_admin
		        ]);
		} else
		{
			$User->update([
		        	'name'=>$user->name,
		        	'email'=>$user->email,
		        	'last_token'=>$token,
		        	'is_admin'=>$user->is_admin
		        ]);
			$User->save();
			
		}

        auth::login($User);

        return redirect($return);
    }

    public function logout(Request $request)
    {
    	auth::logout();


    	return redirect('/');
    }
}
