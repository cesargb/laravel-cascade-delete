<?php

namespace Cesargb\Database\Support;

use Cesargb\Database\Support\Helpers\Helper;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
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
            foreach ($model->getRelationsMorphs() as $methodName) {
                $relation = $model->$methodName();

                if ($relation instanceof MorphOneOrMany) {
                    $relation->delete();
                } elseif ($relation instanceof MorphToMany) {
                    $relation->detach();
                }
            }
        });
    }

    /**
     * Fetch the defined cascading morph deletes for this model.
     *
     * @throws \LogicException
     * @return array
     */
    protected function getRelationsMorphs()
    {
        $methodsToDelete = (array) ($this->cascadeDeleteMorph ?? []);

        return array_filter($methodsToDelete, function ($methodName) {
            return Helper::methodReturnedMorphRelation($this, $methodName);
        });
    }

    public function deleteMorphResidual()
    {
        foreach ($this->getRelationsMorphs() as $method) {
            $relation = $this->$method();

            $relation_type = $relation->getMorphType();

            if ($relation instanceof MorphOneOrMany) {
                $relation_table = $relation->getRelated()->getTable();

                $relation_id = $relation->getForeignKeyName();
            } elseif ($relation instanceof MorphToMany) {
                $relation_table = $relation->getTable();

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
}
