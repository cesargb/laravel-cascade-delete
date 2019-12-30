<?php

namespace Cesargb\Database\Support\Helpers;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LogicException;

class Helper
{
    /**
     * Get the classes that use the trait CascadeDelete
     *
     * @return array
     */
    public static function getClassWithCascadeDeleteTrait()
    {
        return array_filter(
            get_declared_classes(),
            function ($class) {
                return array_key_exists(
                    CascadeDelete::class,
                    class_uses($class)
                );
            }
        );
    }

    /**
     * Check if method of Model return a Morph Relation
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param string                               $methodName
     * @throws \LogicException
     * @return bool
     */
    public static function methodReturnedMorphRelation($model, $methodName)
    {
        if (! method_exists($model, $methodName)) {
            throw new LogicException(
                sprintf(
                    'The model %s not have the method %s',
                    self::class,
                    $methodName
                ),
                10
            );
        }

        $methodIsMorph = $model->$methodName() instanceof MorphOneOrMany || $model->$methodName() instanceof MorphToMany;

        if (! $methodIsMorph) {
            throw new LogicException(
                sprintf(
                    'The relation %s must return an object of type %s, %s or %s',
                    $methodName,
                    MorphOne::class,
                    MorphMany::class,
                    MorphToMany::class
                ),
                20
            );
        }

        return true;
    }

    protected function static isMethodMorph($method)
}