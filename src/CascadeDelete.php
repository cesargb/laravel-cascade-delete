<?php

namespace Cesargb\Database\Support;

use LogicException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CascadeDelete
{
    /**
     * Boot the trait.
     *
     * Listen for the deleted event of a model, and run
     * the delete operation for morphs configured relationship methods.
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
     * @throws \LogicException
     * @return array
     */
    protected function getCascadeDeleteMorphValid()
    {
        return array_filter($this->getCascadeDeleteMorph(), function ($method) {
            if (! method_exists($this, $method)) {
                throw new LogicException(sprintf(
                    'The class %s not have the method %s',
                    self::class,
                    $method
                ));

                return false;
            }

            $relation = $this->$method();

            if (! $relation instanceof MorphMany && ! $relation instanceof MorphToMany) {
                throw new LogicException(sprintf(
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

    public function deleteMorphResidual()
    {
        foreach ($this->getCascadeDeleteMorphValid() as $method) {
            $relation = $this->$method();

            if ($relation instanceof MorphMany) {
                $relation_table = $relation->getRelated()->getTable();

                $relation_type = $relation->getMorphType();

                $relation_id = $relation->getForeignKeyName();
            } elseif ($relation instanceof MorphToMany) {
                $relation_table = $relation->getTable();

                $relation_type = $relation->getMorphType();

                $relation_id = $relation->getForeignPivotKeyName();
            }

            $parents = DB::table($relation_table)
                            ->groupBy($relation_type)
                            ->pluck($relation_type);

            foreach ($parents as $parent) {
                if (class_exists($parent)) {
                    $parentObject = new $parent;

                    DB::table($relation_table)
                            ->where($relation_type, $parent)
                            ->whereNotExists(function ($query) use (
                                $parentObject,
                                $relation_table,
                                $relation_type,
                                $relation_id
                            ) {
                                $query->select(DB::raw(1))
                                        ->from($parentObject->getTable())
                                        ->whereRaw(
                                            $parentObject->getTable().'.'.$parentObject->getKeyName().' = '.$relation_table.'.'.$relation_id
                                        );
                            })->delete();
                } else {
                    DB::table($relation_table)
                            ->where($relation_type, $parent)
                            ->delete();
                }
            }
        }
    }
}
