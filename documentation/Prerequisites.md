Zermelo Reporting Engine Server Prerequisites
========

A PHP reporting engine that works especially well with Laravel, built with love at [CareSet Systems](http://careset.com)

## Complete Prerequisites
- PHP 7.1.+ installed, 7.2.+ preferred (required for nullable type declarations, and soon encrypted zip files)
- Composer Installed. See [Composer Getting Started](https://getcomposer.org/)
- Server requirements for Laravel 5.6:
```
    PHP >= 7.1.3
    OpenSSL PHP Extension
    PDO PHP Extension
    Mbstring PHP Extension
    Tokenizer PHP Extension
    XML PHP Extension
```
- MYSQL server, and user with CREATE TABLE permissions
  
- Installed and functioning Laravel 5.6. See [Laravel 5.6 Installation Instructions](https://laravel.com/docs/5.6/installation)

- Optionally you can use Laravel's Homestead VM and Vagrant to create a VM with all the correct dependencies. See [Laravel Homestead Installation](https://laravel.com/docs/5.6/homestead)

  A good way to start is to use composer to insure you download correct version, do this inside the Homestead Box (vagrant ssh) if you are using Homestead.
  
  ```
  composer create-project laravel/laravel zermelo-demo  "5.6.*" --prefer-dist
  ```

