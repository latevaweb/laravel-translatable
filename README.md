# Laravel Translatable

Make Eloquent model attributes translatables using Translations table

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/latevaweb/laravel-translatable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/latevaweb/laravel-translatable/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/latevaweb/laravel-translatable/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/latevaweb/laravel-translatable/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/latevaweb/laravel-translatable/badges/build.png?b=master)](https://scrutinizer-ci.com/g/latevaweb/laravel-translatable/build-status/master)
[![StyleCI](https://github.styleci.io/repos/229246130/shield?branch=master)](https://github.styleci.io/repos/229246130)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel 6.x](https://img.shields.io/badge/Laravel-6.x-orange.svg)](http://laravel.com)

This package contains a trait to make Eloquent attributes translatable. Translations are stored in Translations database table.

Once the trait is installed on the model you can do these things:

```php
$customer = new Customer; // An Eloquent model
$customer
   ->setTranslation('greeting', 'en', 'Hello')
   ->setTranslation('greeting', 'es', 'Hola')
   ->save();
   
$newsItem->greeting; // Returns 'Hello' given that the current app locale is 'en'
$newsItem->getTranslation('greeting', 'es'); // returns 'Hola'

app()->setLocale('es');

$newsItem->name; // Returns 'Hola'
```

## Installation

You can install the package via composer:

``` bash
composer require latevaweb/laravel-translatable
```