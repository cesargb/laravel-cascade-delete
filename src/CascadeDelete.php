<?php

namespace Cesargb\Database\Support;

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
            Morph::deleteMorphRelationsFromRecordModel($model);
        });
    }

    /**
     * Fetch methods name than must return a morph relation.
     *
     * @return string[]
     */
    public function getCascadeDeleteMorph()
    {
        return (array) ($this->cascadeDeleteMorph ?? []);
    }

    /**
     * Clean residual morph relation from a model. Return number
     * of deleted rows.
     *
     * @return int
     */
    public function deleteMorphResidual()
    {
        return Morph::cleanResidualMorphRelationsFromModel($this);
    }
}
