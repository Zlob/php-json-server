
#PHP JSON Server

Easy to use library, that provides you REST API server in a few minutes.
Created for front-end developers who need a quick back-end for prototyping.

интегрируется во фреймворк

**для прототипирования, а не для продакшена**

Inspired by [JSON Server](https://github.com/typicode/json-server) 

##Features

обработка GET POST PATCH DELETE запросов
простое редактирование ДБ файла

[![Latest Stable Version](https://poser.pugx.org/zlob/php-json-server/v/stable)](https://packagist.org/packages/zlob/php-json-server) [![Total Downloads](https://poser.pugx.org/zlob/php-json-server/downloads)](https://packagist.org/packages/zlob/php-json-server) [![Latest Unstable Version](https://poser.pugx.org/zlob/php-json-server/v/unstable)](https://packagist.org/packages/zlob/php-json-server) [![License](https://poser.pugx.org/zlob/php-json-server/license)](https://packagist.org/packages/zlob/php-json-server)

##Install

via composer: composer.phar require zlob/php-json-server

##Example

You an use this json-server with any php web framework. Here is some examples how to integrate it with Laravel and Symfony

###Laravel 5.1

* First, you need to create controller:

``` php
<?php

namespace App\Http\Controllers;

use Request;
use Config;
use JsonServer\JsonServer;

class JsonServerController extends Controller
{
    public function handleRequest($uri)
    {
        $data = Request::all();                                   //request data
        $method = Request::method();                              //request method
        $jsonServer = new JsonServer();                           //create new JsonServer instance
        return $jsonServer->handleRequest($method, $uri, $data);  //handle request
    }
}
```
* Then, add to routes.php file new route, to link '/api/*' rout with our controller method handleRequest
``` php
Route::any('api/{all}', "JsonServerController@handleRequest")->where('all', '.*');
```
* Thats all! Now,  all requests to 
теперь все запросы к api/* будут транслироваться в php json server

симфони

##Documentation

расписать конфиги

##License
