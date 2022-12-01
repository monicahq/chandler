<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LaravelSabre Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where LaravelSabre will be accessible from. If the
    | setting is null, LaravelSabre will reside under the same domain as the
    | application. Otherwise, this value will be used as the subdomain.
    |
    */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | LaravelSabre Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where LaravelSabre will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */

    'path' => 'dav',

    /*
    |--------------------------------------------------------------------------
    | LaravelSabre Master Switch
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable LaravelSabre.
    |
    */

    'enabled' => (bool) env('DAV_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | LaravelSabre Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every LaravelSabre route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => [
        'api',
        \App\Http\Middleware\EnsureDavRequestsAreStateful::class,
        'abilities:read,write',
    ],

];
