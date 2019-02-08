<?php

function api_prefix()
{
    $api_prefix = trim( config("zermelo.URI_API_PREFIX"), "/ " );
    return $api_prefix;
}

function tabular_api_prefix()
{
    $api_prefix = trim( config("zermelo.TABULAR_API_PREFIX"), "/ " );
    return $api_prefix;
}

function bootstap_css()
{
    if ( config('zermelo.BOOTSTRAP_CSS_LOCATION') ) {
        return asset( config( 'zermelo.BOOTSTRAP_CSS_LOCATION' ) );
    } else {
        return asset('vendor/CareSet/bootstrap/css/bootstrap.min.css');
    }
}


