<?php

/*
 * This file is part of careset/caresetjwtauthclient.
 */

return [

	"a"=>"asdfaf",
	
	'applicaiton_token'=>env("CARESET_TOKEN",""),

	'auth_login'=>env("CARESET_JWT_LOGIN","https://auth.careset.com/auth/cs/login"),

	'callback_url'=>env("CARESET_JWT_CALLBACK",url('/auth/cs/callback')),

	'return_url'=>env("CARESET_JWT_RETURN",url('/')),

    'public_key' => file_get_contents(base_path('keys/jwt_public_key.pub')),

    'algo' => 'HS256'

];
