<?php

function api_prefix()
{
    $api_prefix = trim( config("zermelo.URI_API_PREFIX"), "/ " );
    return $api_prefix;
}


