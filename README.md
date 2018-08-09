# Cascading morph relations deletes for the Laravel PHP Framework

This package add a trait for use in Elocuents Models that permit deletes in
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
