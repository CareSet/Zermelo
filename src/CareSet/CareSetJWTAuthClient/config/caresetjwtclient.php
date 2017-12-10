<?php

/*
 * This file is part of careset/caresetjwtauthclient.
 */

return [
    'public_key' => file_get_contents(base_path('keys/jwt_public_key.pub')),
    'algo' => 'HS256'

];
