<?php

namespace Cesargb\Database\Support\Events;

class RelationMorphFromModelWasCleaned
{
    public $model;

    public $relation;

    public $numDeleted;

    /**
     * Event dispach when clean relations from model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $relation
     * @param int $numDeleted
     */
    public function __construct($model, $relation, $numDeleted)
    {
        $this->model = $model;
        $this->relation = $relation;
        $this->numDeleted = $numDeleted;
    }
}