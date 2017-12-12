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
use CareSet\CareSetJWTAuthClient\Model\User;
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

        if (Auth::guard()->check()) {
    		
    		auth::logout();


            $url = config('caresetjwtclient.auth_logout');
            $token = config('caresetjwtclient.applicaiton_token');
            $callback  = config('caresetjwtclient.callback_url');
            $return = config('caresetjwtclient.return_url');

            $parts = parse_url($url);

            $parameters = [];
            if (isset($parts['query'])) {
                $parameters = self::http_parse_query($parts['query']);
            }
            $parameters['token'] = $token;
            $parameters['callback'] = $callback;
            $parameters['return'] = $return;

            $parts['query'] = http_build_query($parameters);

            $rebuild_redirect = (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') . 
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') . 
            (isset($parts['user']) ? "{$parts['user']}" : '') . 
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') . 
            (isset($parts['user']) ? '@' : '') . 
            (isset($parts['host']) ? "{$parts['host']}" : '') . 
            (isset($parts['port']) ? ":{$parts['port']}" : '') . 
            (isset($parts['path']) ? "{$parts['path']}" : '') . 
            (isset($parts['query']) ? "?{$parts['query']}" : '') . 
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');

            return redirect($rebuild_redirect);

        }

    	return redirect(   );
    }
}
