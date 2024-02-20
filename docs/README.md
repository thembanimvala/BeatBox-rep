<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Install Laravel Global installer

Install global laravel installer for ubuntu from your hom directory

````bash
composer global require "laravel/installer"

vi  ~/.bashrc
````

Add this line: export PATH="$PATH:$HOME/.config/composer/vendor/bin"

````bash
source ~/.bashrc    # reload your shell
````

## Create a new Laravel application

- Goto /data/www

````bash
laravel new filament

cd filament

composer install && composer update
````

### For the admin users we need a working dbase setup and then

````bash
php artisan migrate       // this creates the user tables
````

### Install Filament into base app

````bash
composer require filament/filament:"^3.0-stable" -W

php artisan filament:install --panels
````

Create a user to login to http://yourapp/admin

````bash
php artisan make:filament-user
````

## Form Builder

````bash
composer require filament/forms:"^3.0-stable" -W

php artisan filament:install --scaffold --forms

npm install tailwindcss @tailwindcss/forms @tailwindcss/typography postcss autoprefixer --save-dev

npm run dev                     # keep this open running all the time
npm run build                   # for production

php asrtisan serve              # check the app in your browser
````

### Continue within docs directory

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.
