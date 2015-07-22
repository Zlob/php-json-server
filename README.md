
#PHP JSON Server

Easy to use library, that provides you REST API server in a few minutes.
Created for front-end developers who need a quick back-end for prototyping.

интегрируется во фреймворк

для прототипирования, а не для продакшена

Inspired by [JSON Server](https://github.com/typicode/json-server) 

##Features

обработка GET POST PATCH DELETE запросов
простое редактирование ДБ файла

##Install

Через терминал командой composer require zlob/php-json-server

Теперь, в вашем любимом фреймворке, вам нужно переадресовывать все запросы json-серверу

##Example

You an use this json-server with any php web framework. Here is some examples how to integrate it with Laravel and Symfony

###Laravel 5.1

1. First, you need to create controller:

```
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

2. Then, add to routes.php file new route, to link '/api/*' rout with ouer controller method handleRequest

```
Route::any('api/{all}', "JsonServerController@handleRequest")->where('all', '.*');
```

3. Thats all! Now,  all requests to 
теперь все запросы к api/* будут транслироваться в php json server

симфони

##Documentation

##License
