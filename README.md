# Laravel Cacheable 

# Structure
- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Installation](#installation)
    - [Setup](#setup)
- [Console commands](#console-commands)
- [Methods](#methods)
- [FAQ](#faq)
- [License](#license)
- [Other packages](#other-packages)

<a href="https://www.buymeacoffee.com/kolirt" target="_blank">
  <img src="https://cdn.buymeacoffee.com/buttons/v2/arial-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" >
</a>

# Getting started
Easily cache and control class methods without having to remember cache key names


## Requirements
- PHP >= 8.1
- Laravel >= 10


## Installation
```bash
composer require kolirt/laravel-cacheable
```


## Setup
Publish config file

```bash
php artisan cacheable:install
```

By default, Laravel has a problem with multitags, which leads to excessive duplication of data in the cache and unexpected behavior when clearing and reading the cache. You can read more about it <a href="https://github.com/laravel/framework/issues/25234" target="_blank">here</a>. To fix this issue, add the following code to the `composer.json` and run `composer dump-autoload`

```json
{
    "autoload": {
        "exclude-from-classmap": [
            "vendor/laravel/framework/src/Illuminate/Cache/TaggedCache.php"
        ],
        "files": [
            "vendor/kolirt/laravel-cacheable/src/Overrides/TaggedCache.php"
        ]
    }
}
```

Use the `Cacheable` trait in the target class

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;
}
```


# Console commands
- `cacheable:install` - Install cacheable package
- `cacheable:publish-config` - Publish the config file


# Methods

### `cache`
Using the `cache` method, cache everything you need

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;

    public function exampleMethod()
    {
        return $this->cache(fn () => 'example data');
    }

    public function exampleMethodWithParams(int $id)
    {
        return $this->cache(fn () => 'example data with id ' . $id);
    }
}
```


### `clearCache`
To clear the cache, use the `clearCache` method

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;

    public function clearExampleMethod() 
    {
        $this->clearCache('exampleMethod');
    }

    public function clearExampleMethodWithParams(int $id) 
    {
        $this->clearCache('exampleMethodWithParams', $id);
    }
}
```


### `updateCache`
To update the cache, use the `updateCache` method

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;

    public function updateExampleMethod() 
    {
        $this->updateCache('exampleMethod', 'new example data');
    }
}
```


### `setCacheTime`
To set the cache time, use the `setCacheTime` method

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;

    public function __construct()
    {
        $this->setCacheTime(now()->endOfDay());
    }
}
```


### `flushAllCache`
Clearing the all cache works on tags. You have to switch the class to taggable mode

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;
    
    protected bool $taggable = true;
}
```

Or you can add tags to the class by using the `appendCacheTags` method without taggable mode

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;
    
    public function __construct() {
        $this->appendCacheTags(['tag1', 'tag2']);
    }
}
```

To flush all cache, use the `flushAllCache` method

```php
$example = new Example();
$example->flushAllCache();
```


### `appendCacheTags`
In addition to the basic tag that is added automatically in taggable mode, you can add additional tags that you need using the `appendCacheTags` method

```php
use Kolirt\Cacheable\Traits\Cacheable;

class Example
{
    use Cacheable;

    protected bool $taggable = true;

    /** add additional tags for all methods */
    public function __construct()
    {
        $this->appendCacheTags(['tag1', 'tag2']);
    }

    /** or add additional tags for specific method */
    public function exampleMethod()
    {
        $this->appendCacheTags(['tag1', 'tag2']);
        return $this->cache(fn () => 'example data');
    }
}
```

Then, through Cache facade, you can delete the cache for the tag you need

```php
use Illuminate\Support\Facades\Cache;

Cache::tags(['tag1'])->flush();
Cache::tags(['tag2'])->flush();
Cache::tags(['tag1', 'tag2'])->flush();
```


# FAQ
Check closed [issues](https://github.com/kolirt/laravel-cacheable/issues) to get answers for most asked questions


# License
[MIT](LICENSE.txt)


# Other packages
Check out my other packages on my [GitHub profile](https://github.com/kolirt)
