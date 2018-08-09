<?php

namespace Cesargb\Database\Support;

use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CascadeDelete
{
    /**
     * Boot the trait.
     *
     * Listen for the deleted event of a model, and run
     * the delete operation for morphs configured relationship methods.
     *
     * @throws \LogicException
     */
    protected static function bootCascadeDelete()
    {
        static::deleted(function ($model) {
            foreach ($model->getCascadeDeleteMorphValid() as $method) {
                $relation = $model->$method();

                if ($relation instanceof MorphMany) {
                    $relation->delete();
                } elseif ($relation instanceof MorphToMany) {
                    $relation->detach();
                }
            }
        });
    }

    /**
     * Fetch the valids cascading morphs deletes for this model.
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function getCascadeDeleteMorphValid()
    {
        return array_filter($this->getCascadeDeleteMorph(), function ($method) {
            if (!method_exists($this, $method)) {
                throw new Exception(sprintf(
                    'The class %s not have the method %s',
                    self::class,
                    $method
                ));

                return false;
            }

            $relation = $this->$method();

            if (!$relation instanceof MorphMany && !$relation instanceof MorphToMany) {
                throw new Exception(sprintf(
                    'The relation %s must return an object of type %s or %s',
                    $method,
                    MorphMany::class,
                    MorphToMany::class
                ));

                return false;
            }

            return true;
        });
    }

    /**
     * Fetch the defined cascading morph deletes for this model.
     *
     * @return array
     */
    protected function getCascadeDeleteMorph()
    {
        return (array) ($this->cascadeDeleteMorph ?? []);
    }
}
