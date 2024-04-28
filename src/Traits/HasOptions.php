<?php

namespace Secretwebmaster\LaravelOptionable\Traits;

use Secretwebmaster\LaravelOptionable\Models\Option;

trait HasOptions
{
    public $laravel_optionable_configs;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //plan to add a file: config/laravel-optionable.php
        $this->laravel_optionable_configs = [];
    }

    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }

    public function get_option(string $key, string|int|bool|null $fallback = null, bool|null $fallback_on_empty_value = true)
    {
        $option = $this->options()->where('key', $key)->first();

        if ($option) {
            if (empty($option->value) && $fallback_on_empty_value) {
                return $fallback;
            } else {
                return $option->value;
            }
        }

        return $fallback;
    }

    public function get_options(string $format = 'array')
    {
        if ($format == 'collection') {
            return $this->options;
        } elseif ($format == 'json') {
            return $this->options->pluck('value', 'key')->toJson();
        } else {
            return $this->options->pluck('value', 'key')->toArray();
        }
    }

    public function set_option(string $key, string|int|bool|null $value)
    {
        return $this->options()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public function set_options(array $options)
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

    public function delete_option(string $key)
    {
        return $this->options()->where('key', $key)->delete();
    }

    public function delete_options(array $keys)
    {
        return $this->options()->whereIn('key', $keys)->delete();
    }

    public function delete_all_options(array $except = [])
    {
        return $this->options()->whereNotIn('key', $except)->delete();
    }
}
