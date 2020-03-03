<?php

namespace Cesargb\Database\Support;

use Cesargb\Database\Support\Events\RelationMorphFromModelWasCleaned;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Finder\Finder;

class Morph
{
    /**
     * Get the classes that use the trait CascadeDelete.
     *
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function getCascadeDeleteModels()
    {
        $this->load();

        return array_map(
            function ($modelName) {
                return new $modelName;
            },
            $this->getModelsNameWithCascadeDeleteTrait()
        );
    }

    /**
     * Delete polymorphic relationships of the single records from Model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function delete($model)
    {
        foreach ($this->getValidMorphRelationsFromModel($model) as $relationMorph) {
            if ($relationMorph instanceof MorphOneOrMany) {
                $relationMorph->delete();
            } elseif ($relationMorph instanceof MorphToMany) {
                $relationMorph->detach();
            }
        }
    }

    /**
     * Clean residual polymorphic relationships from all Models.
     *
     * @param bool $dryRun
     * @return int Num rows was deleted
     */
    public function cleanResidual(bool $dryRun = false)
    {
        $numRowsDeleted = 0;

        foreach ($this->getCascadeDeleteModels() as $model) {
            $numRowsDeleted += $this->cleanResidualByModel($model, $dryRun);
        }

        return $numRowsDeleted;
    }

    /**
     * Clean residual polymorphic relationships from a Model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param bool $dryRun
     * @return int Num rows was deleted
     */
    public function cleanResidualByModel($model, bool $dryRun = false)
    {
        $numRowsDeleted = 0;

        $relationsMorphs = $this->getValidMorphRelationsFromModel($model);

        foreach ($relationsMorphs as $relation) {
            if ($relation instanceof MorphOneOrMany) {
                $deleted = $this->cleanResidualFromDB(
                    $relation->getRelated()->getTable(),
                    $relation->getMorphType(),
                    $relation->getForeignKeyName(),
                    $dryRun ? 'count' : 'delete'
                );

                Event::dispatch(new RelationMorphFromModelWasCleaned($model, $relation, $deleted));

                $numRowsDeleted += $deleted;
            } elseif ($relation instanceof MorphToMany) {
                $deleted = $this->cleanResidualFromDB(
                    $relation->getTable(),
                    $relation->getMorphType(),
                    $relation->getForeignPivotKeyName(),
                    $dryRun ? 'count' : 'delete'
                );

                Event::dispatch(new RelationMorphFromModelWasCleaned($model, $relation, $deleted));

                $numRowsDeleted += $deleted;
            }
        }

        return $numRowsDeleted;
    }

    /**
     * Clean residual for a Table.
     *
     * @param string $table      Table with morph relation
     * @param string $fieldType  Field defined for Morph Type
     * @param string $fieldId    Field defined for Morph Id
     * @param string $method     Method to execute `delete` or `count`
     * @return int Num rows was deleted
     */
    protected function cleanResidualFromDB($table, $fieldType, $fieldId, string $method = 'delete')
    {
        $numRowsDeleted = 0;

        $parents = DB::table($table)->groupBy($fieldType)->pluck($fieldType);

        foreach ($parents as $parent) {
            if (class_exists($parent)) {
                $parentObject = new $parent;

                $numRowsDeleted += DB::table($table)
                        ->where($fieldType, $parent)
                        ->whereNotExists(function ($query) use (
                            $parentObject,
                            $table,
                            $fieldId
                        ) {
                            $query->select(DB::raw(1))
                                    ->from($parentObject->getTable())
                                    ->whereRaw(
                                        $parentObject->getTable().'.'.$parentObject->getKeyName().' = '.$table.'.'.$fieldId
                                    );
                        })->$method();
            } else {
                $numRowsDeleted += DB::table($table)
                        ->where($fieldType, $parent)
                        ->$method();
            }
        }

        return $numRowsDeleted;
    }

    /**
     * Get the classes names that use the trait CascadeDelete.
     *
     * @return array
     */
    protected function getModelsNameWithCascadeDeleteTrait()
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
     * Fetch polymorphic relationships from a Model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function getValidMorphRelationsFromModel($model)
    {
        return array_filter(
            array_map(
                function ($methodName) use ($model) {
                    return $this->methodReturnedMorphRelation($model, $methodName);
                },
                $model->getCascadeDeleteMorph()
            ),
            function ($relation) {
                return $relation;
            }
        );
    }

    /**
     * Verify if method of a Model return a polymorphic relationship.
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param string                               $methodName
     * @return bool
     */
    protected function methodReturnedMorphRelation($model, $methodName)
    {
        if (! method_exists($model, $methodName)) {
            return;
        }

        $relation = $model->$methodName();

        return $this->isMorphRelation($relation) ? $relation : null;
    }

    /**
     * Verify if a object is a instance of a polymorphic relationship.
     *
     * @param mixed $relation
     * @return bool
     */
    protected function isMorphRelation($relation)
    {
        return $relation instanceof MorphOneOrMany || $relation instanceof MorphToMany;
    }

    /**
     * Load models with Cascade Delete.
     *
     * @param string|array $path
     * @return void
     */
    protected function load()
    {
        foreach ($this->findModels() as $file) {
            require_once $file->getPathname();
        }
    }

    protected function findModels()
    {
        return Finder::create()
            ->files()
            ->in(config('morph.models_paths', app_path()))
            ->name('*.php')
            ->contains('CascadeDelete');
    }
}
