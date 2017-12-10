<?php

namespace CareSet\CareSetJWTAuthClient\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class JWTClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (!Auth::guard($guard)->check()) {


            $url = config('caresetjwtclient.auth_login');
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
            $parameters['return'] = $request->url();

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

        return $next($request);
    }


    private static function http_parse_query($query) {
        $parameters = array();
        $queryParts = explode('&', $query);
        foreach ($queryParts as $queryPart) {
            $keyValue = explode('=', $queryPart, 2);
            $parameters[$keyValue[0]] = $keyValue[1];
        }
        return $parameters;
    }
}
