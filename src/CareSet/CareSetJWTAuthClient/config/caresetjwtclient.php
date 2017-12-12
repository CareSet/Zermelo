<?php

/*
 * This file is part of careset/caresetjwtauthclient.
 */

return [

	'applicaiton_token'=>env("CARESET_TOKEN",""),

	'auth_login'=>env("CARESET_JWT_LOGIN","https://auth.careset.com/auth/cs/login"),
	
	'auth_logout'=>env("CARESET_JWT_LOGOUT","https://auth.careset.com/auth/cs/logout"),

	'callback_url'=>env("CARESET_JWT_CALLBACK",'https://localhost/auth/cs/callback'),

	'return_url'=>env("CARESET_JWT_RETURN",'https://localhost/'),

    'public_key' => file_get_contents(base_path('keys/jwt_public_key.pub')),

    'algo' => ['RS256']

];
