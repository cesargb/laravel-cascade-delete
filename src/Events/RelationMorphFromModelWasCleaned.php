<?php

namespace Cesargb\Database\Support\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class RelationMorphFromModelWasCleaned
{
    public $model;

    public $relation;

    public $numDeleted;

    public $dryRun;

     /**
      * Event dispach when clean relations morph from model
      *
      * @param Model $model
      * @param Relation $relation
      * @param integer $numDeleted
      * @param boolean $dryRun
      */
    public function __construct(Model $model, Relation $relation, int $numDeleted, bool $dryRun)
    {
        $this->model = $model;
        $this->relation = $relation;
        $this->numDeleted = $numDeleted;
        $this->dryRun = $dryRun;
    }
}
