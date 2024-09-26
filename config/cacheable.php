<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | Add namespace class to key cache
     */
    'namespace' => env('CACHEABLE_NAMESPACE', false),

    /*
    |--------------------------------------------------------------------------
    | Caching time
    |--------------------------------------------------------------------------
    |
    | Supported values: int in minutes, "endOfDay", "endOfHour", "endOfMinute", "endOfMonth", "endOfWeek", "endOfYear"
    |
    */
    'default_cache_time' => env('CACHEABLE_DEFAULT_CACHE_TIME', 24 * 60),

    /*
    |--------------------------------------------------------------------------
    | Disable caching
    |--------------------------------------------------------------------------
    |
    | Disable caching for all
     */
    'disable_cache' => env('CACHEABLE_DISABLE_CACHING', false),
];
