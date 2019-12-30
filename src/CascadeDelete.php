<?php

namespace Cesargb\Database\Support;

use Cesargb\Database\Support\Helpers\Helper;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use LogicException;

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
            foreach ($model->getCascadeDeleteMorph() as $methodName) {
                $relation = $model->$methodName();

                if ($relation instanceof MorphOne || $relation instanceof MorphMany) {
                    $relation->delete();
                } elseif ($relation instanceof MorphToMany) {
                    $relation->detach();
                }
            }
        });
    }

    public function deleteMorphResidual()
    {
        foreach ($this->getCascadeDeleteMorph() as $method) {
            $relation = $this->$method();

            if ($relation instanceof MorphOne || $relation instanceof MorphMany) {
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

    /**
     * Fetch the defined cascading morph deletes for this model.
     *
     * @throws \LogicException
     * @return array
     */
    protected function getCascadeDeleteMorph()
    {
        $methodsToDelete = (array) ($this->cascadeDeleteMorph ?? []);

        return array_filter($methodsToDelete, function ($methodName) {
            Helper::methodReturnedMorphRelation($this, $methodName);

            return true;
        });
    }
}
