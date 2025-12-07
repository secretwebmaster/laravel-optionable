# Laravel Optionable v2.0.0

Laravel Optionable adds flexible, structured option storage to any Eloquent model.
Options are stored in a dedicated table with support for:

* **scope** (e.g. `theme`, `template`, `seo`)
* **group** (e.g. section or nested context)
* **repeatable items** with `sort`
* **nested JSON option values**
* **fallbacks**
* **translation support (via HasTranslations)**

This package powers advanced option systems such as **WNCMS theme options**, **page template options**, and **model metadata**.

---

## Installation

```bash
composer require secretwebmaster/laravel-optionable
php artisan migrate
```

---

# Usage

Add `HasOptions` to any Eloquent model:

```php
use Illuminate\Database\Eloquent\Model;
use Secretwebmaster\LaravelOptionable\Traits\HasOptions;

class Page extends Model
{
    use HasOptions;
}
```

You may now store unlimited structured options on this model.

---

# Getting Options

## Get all rows (optional scope/group)

```php
$rows = $page->getOptions();
$rows = $page->getOptions('theme');
$rows = $page->getOptions('theme', 'header');
```

Returns a sorted `Collection` of Option rows.

---

## Get a single option value

```php
$value = $page->getOption('title', 'theme');
```

Or with group:

```php
$value = $page->getOption('button_text', 'theme', 'hero');
```

Fallback support:

```php
$value = $page->getOption('color', 'theme', 'footer', 'default-color');
```

Allow null values:

```php
$page->getOption('logo', 'theme', null, null, false);
```

---

# Setting Options

## Set a single option

```php
$page->setOption('title', 'Hello World', 'theme');
```

With group and sort:

```php
$page->setOption('image', '/a.jpg', 'theme', 'gallery', 0);
$page->setOption('image', '/b.jpg', 'theme', 'gallery', 1);
```

---

## Set multiple options for a scope/group

```php
$page->setOptions('theme', 'hero', [
    ['key' => 'title', 'value' => 'Welcome'],
    ['key' => 'subtitle', 'value' => 'Enjoy'],
    ['key' => 'button_text', 'value' => 'Click'],
]);
```

This clears existing options under that scope/group first.

---

# Deleting Options

## Delete a single option

```php
$page->deleteOption('title', 'theme');
```

With group and sort:

```php
$page->deleteOption('image', 'theme', 'gallery', 1);
```

---

## Clear all options under a scope/group

```php
$page->clearOptions('theme', 'hero');
```

---

# Table Schema (v2)

The migration creates:

| Column          | Type             | Description                                |
| --------------- | ---------------- | ------------------------------------------ |
| id              | bigint           | primary key                                |
| scope           | string nullable  | Option namespace (e.g. theme/template/seo) |
| group           | string nullable  | Optional subgroup                          |
| key             | string           | Option key                                 |
| sort            | integer nullable | Repeatable index                           |
| value           | text/json        | Value (translated or raw)                  |
| is_translatable | boolean          | Whether value uses HasTranslations         |
| optionable_type | string           | Morph type                                 |
| optionable_id   | bigint           | Morph id                                   |
| timestamps      | —                | —                                          |

Indexes:

* `scope + group + key + sort + optionable_type + optionable_id`
* polymorphic indexes

## Legacy Support (v1)

Snake_case method names continue to work for all methods that still exist
in v2, thanks to the __call() snake_case fallback.

Supported snake_case aliases:
- get_option()
- set_option()
- get_options()
- set_options()
- delete_option()
- clear_options()

The following v1 methods have been removed and no longer exist:
- deleteOptions()
- deleteAllOptions()
Therefore their snake_case forms (delete_options(), delete_all_options()) are not available.
