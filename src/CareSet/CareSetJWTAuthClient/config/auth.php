<?php

return [

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \CareSet\CareSetJWTAuthClient\Model\User::class,
        ],
    ]

];
