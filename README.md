[![Build Status](https://travis-ci.org/cesargb/laravel-cascade-delete.svg?branch=master)](https://travis-ci.org/cesargb/laravel-cascade-delete)
[![StyleCI](https://github.styleci.io/repos/144183283/shield?branch=master)](https://github.styleci.io/repos/144183283)

# Cascading eliminations implemented in polymorphic relationships for the Laravel apps

This package permit add a trait for use in Elocuents Models that deletes in
cascade the Polymorphic Relations (`MorphMany` or `MorphToMany`).

## Instalation

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
use App\Option;
use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class Video extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['tags', 'options'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function options()
    {
        return $this->morphMany(Option::class, 'optionable');
    }
}
```

Now you can delete an `App\Video` record, and any associated `App\Tag` and
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


## Contributing

Any contributions are welcome.
