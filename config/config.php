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

    'pathToDb' => '/../db/db.json',



];
