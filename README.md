CareSet
========


1. composer update
2. `php artisan vendor:publish --provider="CareSet\\CareSetJWTAuthClient\\ServiceProvider"`
3. edit `config/auth.php`, change "providers.users.model" to `\CareSet\CareSetJWTAuthClient\Model\User::class`
4. edit `.env` to add the following section -
```
CARESET_TOKEN={{key goes here}}
CARESET_JWT_LOGIN=https://auth.careset.com/auth/cs/login
CARESET_JWT_LOGOUT=https://auth.careset.com/auth/cs/logout
CARESET_JWT_CALLBACK={{callback goes here}}
CARESET_JWT_RETURN={{default return url goes here}}
```


To use
=======

Wrap protected routes in middleware

```
Route::middleware([\CareSet\CareSetJWTAuthClient\Middleware\JWTClientMiddleware::class])->group(function () {

	Route::get('/test',function() {

		return 'Hello World!';

	});

});
```
