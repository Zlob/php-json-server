<?php

define( 'singular', 'singularize' );
define( 'plural', 'pluralize' );

return [

    /*
    |--------------------------------------------------------------------------
    | URL naming form
    |--------------------------------------------------------------------------
    |
    | Определяет, в каком числе будут представлены ресурсы в строке URL.
    | К примеру, plural чтобы использовать URL вида 'test.com/api/posts'
    | или singular чтобы использовать URL вида 'test.com/api/post'
    |
    | Specifies in which form resources in the url string
    | In example, 'test.com/api/posts' for plural form
    | or 'test.com/api/post' for singular form
    |
    |
    */

    'urlNamingForm' => plural,

    /*
    |--------------------------------------------------------------------------
    | Table naming form
    |--------------------------------------------------------------------------
    |
    | Определяет, в каком числе будут именоватся ресурсы в БД.
    | К примеру, plural чтобы ресурсы именовались в множественном числе: posts, users и т.д.
    | или singular чтобы ресурсы именовались в единственном числе: post, user и т.д.
    |
    | Specifies form of resources in the url string
    | In example, 'test.com/api/posts' for plural form
    | or 'test.com/api/post' for singular form
    |
    */

    'tableNamingForm' => plural,

    /*
    |--------------------------------------------------------------------------
    | Relations naming form
    |--------------------------------------------------------------------------
    |
    | Определяет, в каком числе будут именоватся связи ресурсов в БД.
    | К примеру, plural чтобы связи ресурсов именовались в множественном числе: posts_id, users_id и т.д.
    | или singular чтобы связи ресурсов именовались в единственном числе: post_id, user_id и т.д.
    |
    | Defines form of resources in the url string
    | In example, 'test.com/api/posts' for plural form
    | or 'test.com/api/post' for singular form
    |
    */

    'relationsNamingForm' => singular,



];
