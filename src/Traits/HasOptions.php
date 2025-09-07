<?php

namespace Secretwebmaster\LaravelOptionable\Traits;

use Illuminate\Support\Str;
use Secretwebmaster\LaravelOptionable\Models\Option;

trait HasOptions
{
    public $laravelOptionableConfigs;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // plan to add a file: config/laravel-optionable.php
        $this->laravelOptionableConfigs = [];
    }

    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }

    public function getOption(string $key, mixed $fallback = null, bool $fallbackOnEmptyValue = true): mixed
    {
        $option = $this->options->firstWhere('key', $key);

        if ($option) {
            if ($option->value === null && $fallbackOnEmptyValue) {
                return $fallback;
            }
            return $option->value;
        }

        return $fallback;
    }

    public function getOptions(string $format = 'array'): mixed
    {
        if ($format === 'collection') {
            return $this->options;
        } elseif ($format === 'json') {
            return $this->options->pluck('value', 'key')->toJson();
        }
        return $this->options->pluck('value', 'key')->toArray();
    }

    public function setOption(string $key, mixed $value)
    {
        return $this->options()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public function setOptions(array $options): int
    {
        $count = 0;
        foreach ($options as $key => $value) {
            $updated = $this->options()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );

            if ($updated) {
                $count++;
            }
        }
        return $count;
    }

    public function deleteOption(string $key): int
    {
        return $this->options()->where('key', $key)->delete();
    }

    public function deleteOptions(array $keys): int
    {
        return $this->options()->whereIn('key', $keys)->delete();
    }

    public function deleteAllOptions(array $except = []): int
    {
        return $this->options()->when(!empty($except), fn($q) => $q->whereNotIn('key', $except))->delete();
    }

    /**
     * Legacy support: map snake_case calls (get_option) to camelCase (getOption).
     */
    public function __call($method, $parameters)
    {
        $snakeMethod = Str::snake($method);

        if (method_exists($this, $snakeMethod)) {
            return $this->{$snakeMethod}(...$parameters);
        }

        return parent::__call($method, $parameters);
    }
}
