# Laravel Optionable

Allow any Eloquent model to have flexible **options** (like user settings, page options, metadata, etc.).  
Options are stored in a dedicated table and can be retrieved, updated, or deleted easily.

## Installation

Install via [Composer](http://getcomposer.org/):

```bash
composer require secretwebmaster/laravel-optionable
````

Run the migration to create the `options` table:

```bash
php artisan migrate
```

That’s all you need 🎉

---

## Overview

* [Get all options](#get-all-options)
* [Get single option value](#get-single-option-value)
* [Set single option](#set-single-option)
* [Set multiple options](#set-multiple-options)
* [Delete single option](#delete-single-option)
* [Delete multiple options](#delete-multiple-options)
* [Delete all options](#delete-all-options)

---

## Usage

Add the `HasOptions` trait to any Eloquent model.
Example: `Post` model

```php
use Illuminate\Database\Eloquent\Model;
use Secretwebmaster\LaravelOptionable\Traits\HasOptions;

class Post extends Model
{
    use HasOptions;  // <-- add this

    //...
}
```

Now you can manage options directly on the model instance:

```php
$post = Post::first();
```

---

### Get all options

```php
$post->getOptions(); // default: array
$post->getOptions('json'); // return JSON
$post->getOptions('collection'); // return Collection
```

---

### Get single option value

```php
$post->getOption('key');
```

With fallback:

```php
$post->getOption('key', 'default');
```

If you want to allow `null`/empty values (instead of fallback):

```php
$post->getOption('key', 'default', false);
```

---

### Set single option

```php
$post->setOption('key', 'value');
$post->setOption('theme', ['color' => 'blue']); // arrays/objects supported (stored as JSON)
```

---

### Set multiple options

```php
$post->setOptions([
    'language' => 'English',
    'mode' => 'dark',
    'homepage' => 'welcome',
]);
```

---

### Delete single option

```php
$post->deleteOption('key');
```

---

### Delete multiple options

```php
$post->deleteOptions(['key1', 'key2']);
```

---

### Delete all options

```php
$post->deleteAllOptions();
```

Or keep some keys:

```php
$post->deleteAllOptions(['language']); // deletes everything except 'language'
```

---

## Legacy Support

For backward compatibility, all methods are also available in **snake\_case**:

```php
$post->get_option('key');
$post->set_option('key', 'value');
$post->delete_all_options();
```

Both camelCase and snake\_case will work ✅

---

## Table Schema

The migration creates an `options` table with:

* `id`
* `key` (string)
* `value` (json, nullable)
* `optionable_type` (string)
* `optionable_id` (unsignedBigInteger)
* `timestamps`

Constraints & Indexes:

* Unique per model: `optionable_type + optionable_id + key`
* Indexed `key` column
* Indexed polymorphic relation via `morphs()`

---

## License

MIT © [secretwebmaster](https://github.com/secretwebmaster)

```