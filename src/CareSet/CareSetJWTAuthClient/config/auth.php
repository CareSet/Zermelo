<?php

/*
 * This file is part of careset/caresetjwtauthclient.
 */

return [
	'providers' => [
		'users' => [
			'driver' => 'eloquent',
			'model' => '\CareSet\CareSetJWTAuthClient\Model\User',
		]
	]
];
