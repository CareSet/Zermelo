CareSet
========


1. composer update
2. `php artisan vendor:publish --provider="CareSet\\CareSetJWTAuthClient\\ServiceProvider"`
3. edit `config/auth.php`, change "providers.users.model" to `\CareSet\CareSetJWTAuthClient\Model\User::class`



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
