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
            $morph = new Morph();

            $morph->delete($model);
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
        $morph = new Morph();

        return $morph->cleanResidualByModel($this);
    }
}
