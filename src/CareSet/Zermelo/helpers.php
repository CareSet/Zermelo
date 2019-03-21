<?php

function api_prefix()
{
    $api_prefix = trim( config("zermelo.API_PREFIX"), "/ " );
    return $api_prefix;
}

function tabular_api_prefix()
{
    $api_prefix = trim( config("zermelo.TABULAR_API_PREFIX"), "/ " );
    return $api_prefix;
}

function graph_api_prefix()
{
    $api_prefix = trim( config("zermelo.GRAPH_API_PREFIX"), "/ " );
    return $api_prefix;
}

function zermelo_db()
{
    $db = config("zermelo.ZERMELO_DB" );
    return $db;
}

function report_path()
{
    $reportNS = config("zermelo.REPORT_NAMESPACE" );
    $parts = explode("\\", $reportNS );
    return app_path($parts[count($parts)-1]);
}

function bootstap_css()
{
    if ( config('zermelo.BOOTSTRAP_CSS_LOCATION') ) {
        return asset( config( 'zermelo.BOOTSTRAP_CSS_LOCATION' ) );
    } else {
        return asset('vendor/CareSet/bootstrap/css/bootstrap.min.css');
    }
}


