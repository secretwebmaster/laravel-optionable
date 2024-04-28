## Laravel Optionable

Allow any Eloquent model to have options such as user options, page options, etc.

## Installation

Install the package through [Composer](http://getcomposer.org/). 

Run the Composer require command from the Terminal:

```composer require secretwebmaster/laravel-optionable```

Then run the migration to create our migration table

```php artisan migrate```
    
If you're using Laravel 5.5 or above, that's all. 

If you still be on Laravel with version below 5.4 , there is one more step. Add the following service provider of the package to the package in `config/app.php` file.

Add a new line to the `providers` array:

```Secretwebmaster\LaravelOptionable\PackageServiceProvider::class```

Now you are ready to start using the laravel optionable!


## Usage
First. Add the `HasOptions` trait to your model. Let's take User model as example

```
use Secretwebmaster\LaravelOptionable\Traits\HasOptions;
class Post extends Model
{
    use HasFactory;
    use HasOptions; // <-- add this

    protected $guarded = [];

    //...
}
```


Now you can access all relationship methods. In your real project. You can use on any Eloquent model.

First. Get your model
```
$model = Model::first();
```

### Get all options

```
$model->get_options();
```

You can specify the output format. By default, it will be in array. You can pass `json` or `collection` to change the output format
```
$model->get_options('json');
$model->get_options('collection');
```

### Get single option value

Pass key name to get the value
```
$model->get_option('key');
```

You can also pass a fallback value if key is not found or value is empty.

```
$model->get_option('key', 'fallback value');
```

If you don't want to fallback when key is set but value is empty. You can pass `false` as the third parameter to force return the actual value.
```
$model->get_option('key', 'fallback value', false);
```

### Set single option
```
$model->set_option('key', 'value');
```

### Set multiple options
Pass the data in form of array. Nested array is not supported
```
$model->set_options([
    'language' => 'English',
    'mode' => 'dark',
    'homepage' => 'something',
]);
```

### Delete single option
Pass the key you want to delete
```
$model->delete_option('key');
```

### Delete multiple options
Pass the keys in form of array
```
$model->delete_options(['key1', 'key2']);
```

### Delete all options
```
$model->delete_all_options();
```
