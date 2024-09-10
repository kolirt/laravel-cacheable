<?php

namespace Kolirt\Cacheable\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

trait Cacheable
{

    protected mixed $append_cache_tags = [];

    protected Carbon $cache_time;

    public function __construct()
    {
        switch (config('cacheable.cache_time')) {
            case 'endOfDay':
                $this->setCacheTime(now()->endOfDay());
                break;
            case 'endOfHour':
                $this->setCacheTime(now()->endOfHour());
                break;
            case 'endOfMinute':
                $this->setCacheTime(now()->endOfMinute());
                break;
            case 'endOfMonth':
                $this->setCacheTime(now()->endOfMonth());
                break;
            case 'endOfWeek':
                $this->setCacheTime(now()->endOfWeek());
                break;
            case 'endOfYear':
                $this->setCacheTime(now()->endOfYear());
                break;
            default:
                $this->setCacheTime(now()->addMinutes(config('cacheable.cache_time')));
                break;
        }
    }

    public function appendCacheTags(array $tags): self
    {
        $this->append_cache_tags = array_merge($this->append_cache_tags, $tags);
        return $this;
    }

    protected function setCacheTime(Carbon $time): void
    {
        $this->cache_time = $time;
    }

    protected function cache(Closure $fnc, ...$args)
    {
        if (count($args) === 0) {
            $data = $this->getCacheKey();
        } else {
            $data = $this->getCacheKey(null, ...$args);
        }

        if (count($data['tags'])) {
            $cache = Cache::tags($data['tags']);
        } else {
            $cache = Cache::getFacadeRoot();
        }

        return $cache->remember($data['key'], $this->cache_time, $fnc);
    }

    protected function updateCache($fnc_name, $cached_data)
    {
        $data = $this->getCacheKey($fnc_name);

        if (count($data['tags'])) {
            $cache = Cache::tags($data['tags']);
        } else {
            $cache = Cache::getFacadeRoot();
        }

        return $cache->set($data['key'], $cached_data, $this->cache_time);
    }

    protected function clearCache($fnc_name, ...$args): bool
    {
        $data = $this->getCacheKey($fnc_name, ...$args);

        if (count($data['tags'])) {
            $cache = Cache::tags($data['tags']);
        } else {
            $cache = Cache::getFacadeRoot();
        }

        return $cache->forget($data['key']);
    }

    public function flushAllCache(): bool
    {
        $tags = $this->getCacheTags();

        if (count($tags)) {
            return Cache::tags($tags)->flush();
        }

        return false;
    }

    protected function getCacheTags()
    {
        $tags = $this->append_cache_tags;

        if (property_exists($this, 'taggable') && $this->taggable) {
            $tags[] = $this->getName();
        }

        return $tags;
    }

    private function getCacheKey($fnc_name = null, ...$args): array
    {
        if ($fnc_name === null && count($args) === 0) {
            $backtrace = debug_backtrace()[2];
            $fnc_name = $backtrace['function'];
            $args = $backtrace['args'];
        } else if ($fnc_name === null && count($args) > 0) {
            $backtrace = debug_backtrace()[2];
            $fnc_name = $backtrace['function'];
        }

        $result = [
            $this->getName()
        ];

        $tags = $this->getCacheTags();
        $args_prepared = [];
        foreach ($args as $arg) {
            if ($arg instanceof Pivot) {
                $args_prepared[] = "{$this->getName($arg)}->{$arg->getAttribute($arg->getForeignKey())}_{$arg->getKey()}";
            } else if ($arg instanceof Model) {
                $args_prepared[] = "{$this->getName($arg)}->{$arg->getKey()}";
            } else if ($arg instanceof \UnitEnum) {
                $args_prepared[] = "{$this->getName($arg)}->{$arg->name}";
            } else if ($arg instanceof Carbon) {
                $args_prepared[] = str_replace(':', '_', $arg->toIso8601String());
            } else {
                switch (gettype($arg)) {
                    case 'boolean':
                        $args_prepared[] = $arg ? 1 : 0;
                        break;
                    case 'integer':
                    case 'double':
                    case 'string':
                        $args_prepared[] = $arg;
                        break;
                    case 'NULL':
                        $args_prepared[] = 'null';
                        break;
                }
            }
        }
        $args_prepared = implode(', ', $args_prepared);

        $result[] = "{$fnc_name}($args_prepared)";
        $result[] = 'salt';

        return [
            'tags' => $tags,
            'key' => implode(':', $result)
        ];
    }

    private function getName($target = null): string
    {
        $reflect = new ReflectionClass(is_null($target) ? $this : $target);
        return config('cacheable.namespace') ? $reflect->getName() : $reflect->getShortName();
    }

}
