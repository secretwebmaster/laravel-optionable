<?php

namespace Secretwebmaster\LaravelOptionable\Traits;

use Secretwebmaster\LaravelOptionable\Models\Option;

trait HasOptions
{
    public $laravel_optionable_configs;
    
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->laravel_optionable_configs = [
            'aaa' => 'aaaaaa',
        ];
    }

    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }

    public function get_option($key, $fallback = null)
    {
        return $this->options()->where('key', $key)->first()?->value ?? $fallback;
    }

    public function get_options($format = 'array')
    {
        if($format == 'collection'){
            return $this->options;
        }elseif($format == 'json'){
            return $this->options->pluck('value', 'key')->toJson();
        }else{
            return $this->options->pluck('value', 'key')->toArray();
        }
    }

    public function set_option($key, $value)
    {
        return $this->options()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public function set_options($options)
    {
        $count = 0;
        foreach($options as $key => $value){
            $updated = $this->options()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );

            if($updated){
                $count++;
            }
        }
        return $count;
    }

    public function delete_option($key)
    {
        return $this->options()->where('key', $key)->delete();
    }
}