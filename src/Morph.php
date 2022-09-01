<?php

namespace Cesargb\Database\Support;

use Cesargb\Database\Support\Events\RelationMorphFromModelWasCleaned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Finder\Finder;

class Morph
{
    /**
     * Delete polymorphic relationships of the single records from Model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
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
     * @param  bool  $dryRun
     * @return int Num rows was deleted
     */
    public function cleanResidualAllModels(bool $dryRun = false)
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
     * @param  Model  $model
     * @param  bool  $dryRun
     * @return int Num rows was deleted
     */
    public function cleanResidualByModel($model, bool $dryRun = false)
    {
        $numRowsDeleted = 0;

        foreach ($this->getValidMorphRelationsFromModel($model) as $relation) {
            if ($relation instanceof MorphOneOrMany || $relation instanceof MorphToMany) {
                $deleted = $this->queryCleanOrphan($model, $relation, $dryRun);

                if ($deleted > 0) {
                    Event::dispatch(
                        new RelationMorphFromModelWasCleaned($model, $relation, $deleted, $dryRun)
                    );
                }

                $numRowsDeleted += $deleted;
            }
        }

        return $numRowsDeleted;
    }

    /**
     * Get the classes that use the trait CascadeDelete.
     *
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    protected function getCascadeDeleteModels()
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
     * Query to clean orphan morph table.
     *
     * @param  Model  $parentModel
     * @param  MorphOneOrMany|MorphToMany  $relation
     * @param  bool  $dryRun
     * @return int Num rows was deleted
     */
    protected function queryCleanOrphan(Model $parentModel, Relation $relation, bool $dryRun = false)
    {
        [$childTable, $childFieldType, $childFieldId] = $this->getStructureMorphRelation($relation);

        $method = $dryRun ? 'count' : 'delete';

        return DB::table($childTable)
                ->where($childFieldType, $parentModel->getMorphClass())
                ->whereNotExists(function ($query) use (
                    $parentModel,
                    $childTable,
                    $childFieldId
                ) {
                    $query->select(DB::raw(1))
                            ->from($parentModel->getTable())
                            ->whereRaw(
                                $parentModel->getTable().'.'.$parentModel->getKeyName().' = '.$childTable.'.'.$childFieldId
                            );
                })->$method();
    }

    /**
     * Get table and fields from morph relation.
     *
     * @param  MorphOneOrMany|MorphToMany  $relation
     * @return array [$table, $fieldType, $fieldId]
     */
    protected function getStructureMorphRelation(Relation $relation): array
    {
        $fieldType = $relation->getMorphType();

        if ($relation instanceof MorphOneOrMany) {
            $table = $relation->getRelated()->getTable();
            $fieldId = $relation->getForeignKeyName();
        } elseif ($relation instanceof MorphToMany) {
            $table = $relation->getTable();
            $fieldId = $relation->getForeignPivotKeyName();
        }

        return [$table, $fieldType, $fieldId];
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
     * @param  \Illuminate\Database\Eloquent\Model  $model
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
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $methodName
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
     * @param  mixed  $relation
     * @return bool
     */
    protected function isMorphRelation($relation)
    {
        return $relation instanceof MorphOneOrMany || $relation instanceof MorphToMany;
    }

    /**
     * Load models with Cascade Delete.
     *
     * @param  array|string  $path
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
