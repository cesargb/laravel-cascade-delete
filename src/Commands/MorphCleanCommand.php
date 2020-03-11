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

        if ((new Morph())->cleanResidualAllModels($this->option('dry-run')) === 0) {
            $this->info("\tIt's already cleaned");
        }
    }

    protected function captureEvents()
    {
        Event::listen(
            RelationMorphFromModelWasCleaned::class,
            function (RelationMorphFromModelWasCleaned $event) {
                $this->info(sprintf(
                    "\t✔ Clean model %s in the table %s: %d %s.",
                    get_class($event->model),
                    $event->relation->getRelated()->getTable(),
                    $event->numDeleted,
                    $event->dryRun ? 'rows to remove' : 'rows cleaned'
                ));
            }
        );
    }
}
