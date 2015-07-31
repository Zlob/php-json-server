
#PHP JSON Server

Easy to use library, that provides you REST API server in a few minutes.
Created for front-end developers who need a quick back-end for prototyping.
May be used with you lovely php web framework

Inspired by [JSON Server](https://github.com/typicode/json-server) 

####**NOT FOR PRODUCTION**

[![Latest Stable Version](https://poser.pugx.org/zlob/php-json-server/v/stable)](https://packagist.org/packages/zlob/php-json-server) 
[![Total Downloads](https://poser.pugx.org/zlob/php-json-server/downloads)](https://packagist.org/packages/zlob/php-json-server)
[![License](https://poser.pugx.org/zlob/php-json-server/license)](https://packagist.org/packages/zlob/php-json-server)
[![Build Status](https://travis-ci.org/Zlob/php-json-server.svg?branch=master)](https://travis-ci.org/Zlob/php-json-server)

##Install

via composer: ```composer require zlob/php-json-server```

##Example

You can use this library with any php web framework. Here is example how to integrate it with Laravel 5.1:

* First, you need to create controller, where we will use php-json-server:

``` php
<?php

namespace App\Http\Controllers;

use Request;
use Response;
use Config;
use JsonServer\JsonServer;

class JsonServerController extends Controller
{
    public function handleRequest($uri)
    {
        $data = Request::all();                                             //request data
        $method = Request::method();                                        //request method
        $jsonServer = new JsonServer();                                     //create new JsonServer instance
        $response = $jsonServer->handleRequest($method, $uri, $data);       //handle request
                                                                            //return Symfony\Component\HttpFoundation\Response
                                                                            //object with content, status and headers
        $response->send();                                                  //send response
    }
}
```
* Then, add to routes.php file new route, to link '/api/*' route with our controller method handleRequest
``` php
Route::any('api/{all}', "JsonServerController@handleRequest")->where('all', '.*');
```
* Finaly, we can optionaly fill our database with some data. To do this, open php-json-server/db/db.json file and add some data:
``` json
{
    "posts": [
        {
            "id": 1,
            "title": "json-server",
            "author": "zlob"
        },
        {
            "id": 2,
            "title": "json-server",
            "author": "zlob"
        }
    ],
    "comments": [
        {
            "id": 1,
            "body": "some comment",
            "post_id": 1
        }
    ]
}
```
* Thats all! Now, if you go to "/api/posts" you'll get
{ "id": 1, "title": "json-server", "author": "zlob" }

Based on the previous db.json file, here are all routes:
```
GET    /posts
GET    /posts/1
GET    /posts/1/comments
POST   /posts
PUT    /posts/1
PATCH  /posts/1
DELETE /posts/1
```

To filter resources

```
GET /posts?title=json-server&author=zlob
```

To slice resources, add `_start` and `_end` or `_limit`(an X-Total-Count header is included in the response).

```
GET /posts?_start=20&_end=30
GET /posts/1/comments?_start=0&_end=30
```

To sort resources, add `_sort` and `_order` (ascending order by default).

```
GET /posts?_sort=views&_order=desc
GET /posts/1/comments?_sort=votes&_order=asc
```

To make a full-text search on resources, add `_query`.

```
GET /posts?_query=internet
```

To embed other resources, add `_embed`(separate by ',' for more then one resources).

```
GET /posts/1?_embed=comments
GET /posts/1?_embed=comments,tags
```

##CLI: Generate random data
You can fill database with fake data in one command.
In example, to create table posts, with 50 resources, that contains fields title, date, author and content,
in jsonServer directory use command:
```
php-json-server faker posts 'title.text date.date author.name content.text.10' --num=50
```
Where where fields separated by space, and field name, field type and additional parameter separated by '.'
Field type and parameters described in [faker](https://github.com/fzaninotto/Faker), that used inside.

##License
MIT - [Zlob](https://github.com/zlob)
