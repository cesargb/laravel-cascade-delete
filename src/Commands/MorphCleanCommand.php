<?php

namespace Cesargb\Database\Support\Commands;

use Cesargb\Database\Support\Events\RelationMorphFromModelWasCleaned;
use Cesargb\Database\Support\Morph;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class MorphCleanCommand extends Command
{
    protected $signature = 'morph:clean
                                {--dry-run : test clean}';

    protected $description = 'Clean break relations morph';

    public function handle()
    {
        $this->captureEvents();

        $morph = new Morph();

        foreach ($morph->getCascadeDeleteModels() as $model) {
            $this->info(sprintf('Clean from model %s:', get_class($model)));

            $numRows = $model->deleteMorphResidual($this->option('dry-run'));

            if ($numRows === 0) {
                $this->info("\tIt's already cleaned");
            }
        }
    }

    protected function captureEvents()
    {
        Event::listen(
            RelationMorphFromModelWasCleaned::class,
            function (RelationMorphFromModelWasCleaned $event) {
                $this->comment(sprintf(
                    "\t- From relation %s: %d %s",
                    $event->relation->getRelated()->getTable(),
                    $event->numDeleted,
                    $event->dryRun ? 'rows to remove' : 'rows cleaned'
                ));
            }
        );
    }
}
