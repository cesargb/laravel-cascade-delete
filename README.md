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
use Illuminate\Database\Eloquent\Model;
use Cesargb\Database\Support\CascadeDelete;

class Video extends Model
{
    use CascadeDelete;

    protected $cascadeDeleteMorph = ['tags'];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
```

Now you can delete an `App\Video` record, and any associated `App\Tag` records
will be deleted.

## Contributing

Any contributions are welcome.
