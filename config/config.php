<?php

define( 'singular', 'singularize' );
define( 'plural', 'pluralize' );

return [

    /*
    |--------------------------------------------------------------------------
    | URL naming form
    |--------------------------------------------------------------------------
    |
    | Specifies resources form in the url string
    | For example, plural for URL like 'test.com/api/posts'
    | or singular for URL like 'test.com/api/post'
    |
    | Available Settings: singular, plural
    |
    */

    'urlNamingForm' => plural,

    /*
    |--------------------------------------------------------------------------
    | Table naming form
    |--------------------------------------------------------------------------
    |
    | Specifies form of table in the database
    | In example, 'posts' for plural form
    | or 'post' for singular form
    |
    | Available Settings: singular, plural
    |
    */

    'tableNamingForm' => plural,

    /*
    |--------------------------------------------------------------------------
    | Relations naming form
    |--------------------------------------------------------------------------
    |
    | Specifies form of relation fields in the database
    | In example, 'posts_id' for plural form
    | or 'post_id' for singular form
    |
    | Available Settings: singular, plural
    |
    */

    'relationsNamingForm' => singular,


    /*
    |--------------------------------------------------------------------------
    | Path ro jsonDB file
    |--------------------------------------------------------------------------
    |
    | Specifies path ro jsonDB file
    | In example, 'posts_id' for plural form
    | or 'post_id' for singular form
    |
    | Available Settings: singular, plural
    |
    */

    'pathToDb' => '/db/db.json',

    /*
    |--------------------------------------------------------------------------
    | Fields auto sorting
    |--------------------------------------------------------------------------
    |
    | By default, fields in row sorted automatically
    | with 'fieldsAutoSortingFunc' function (described below)
    | before saving and fetching.
    |
    | In case of false value, rows will be stored in db
    | and displayed in order they came co JsonServer
    |
    */

    'fieldsAutoSorting' => true,


    /*
    |--------------------------------------------------------------------------
    | Fields auto sorting function
    |--------------------------------------------------------------------------
    |
    | Default fields sorting function.
    | Used as second param in uksort() function
    |
    */

    'fieldsAutoSortingFunc' =>  function ($a, $b)
    {
        if ($a === $b){
            return 0;
        }
        if ($a === 'id'){
            return -1;
        }
        if ($b === 'id'){
            return 1;
        }
        if ($a > $b){
            return 1;
        }
        if ($a < $b){
            return -1;
        }
    },

    /*
    |--------------------------------------------------------------------------
    | 'Resource not found' function
    |--------------------------------------------------------------------------
    |
    | Default behaviour in case 'resource not found'
    |
    */

    'resourceNotFound' =>  function ($response)
    {
        $response->setContent('');
        $response->setStatusCode(404);
        return $response;
    },


];
