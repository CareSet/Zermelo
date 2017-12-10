<?php


return [

    'defaults' => [
        'guard' => 'careset',
        'passwords' => 'users',
    ],

   'guards' => [
        'careset' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
	        'users' => [
	            'driver' => 'eloquent',
	            'model' => \CareSet\CareSetJWTAuthClient\Model\User::class,
	        ]
	]


];


