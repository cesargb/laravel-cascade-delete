[![tests](https://github.com/cesargb/laravel-cascade-delete/actions/workflows/tests.yml/badge.svg)](https://github.com/cesargb/laravel-cascade-delete/actions/workflows/tests.yml)
[![phpstan](https://github.com/cesargb/laravel-cascade-delete/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cesargb/laravel-cascade-delete/actions/workflows/phpstan.yml)
[![style-fix](https://github.com/cesargb/laravel-cascade-delete/actions/workflows/style-fix.yml/badge.svg)](https://github.com/cesargb/laravel-cascade-delete/actions/workflows/style-fix.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/cesargb/laravel-cascade-delete.svg?style=flat-square)](https://scrutinizer-ci.com/g/cesargb/laravel-cascade-delete)
[![Total Downloads](https://img.shields.io/packagist/dt/cesargb/laravel-cascade-delete.svg?style=flat-square)](https://packagist.org/packages/cesargb/laravel-cascade-delete)

# Cascading eliminations implemented in polymorphic relationships for the Laravel apps

This package permit add a trait for use in Elocuents Models that deletes in
cascade the Polymorphic Relations (`MorphOne`, `MorphMany` or `MorphToMany`).

## Installation

This package can be used in Laravel 5.5 or higher.

You can install the package via composer:

```bash
composer require cesargb/laravel-cascade-delete
```

## Use

Use the trait `Cesargb\Database\Support\CascadeDelete` in your Elocuent Model and define de property `cascadeDeleteMorph` whith one String or Array with your methods than define the Polymorphic Relations.

## Code Sample

```php
<?php

namespace App;

use App\Tag;
use App\Image;
use App\Option;
use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class Video extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['image', 'tags', 'options'];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
```

Now you can delete an `App\Video` record, and any associated `App\Image`, `App\Tag` and
`App\Options` records will be deleted.

## Delete Residuals

If you bulk delete a model with morphological relationships, you will have
residual data that has not been deleted.

To clean this waste you have the method `deleteMorphResidual`

Sample:

```php
Video::query()->delete();

$video = new Video;

$video->deleteMorphResidual();
```

### Command to remove all residuals

You can use Artisan command `morph:clean` to remove all residuals data from all
your Moldes that used the `Cesargb\Database\Support\CascadeDelete` trait.

```php
php artisan morph:clean
```

## Contributing

Any contributions are welcome.
