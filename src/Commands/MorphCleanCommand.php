<?php

namespace Cesargb\Database\Support\Commands;

use Cesargb\Database\Support\Morph;
use Illuminate\Console\Command;

class MorphCleanCommand extends Command
{
    protected $signature = 'morph:clean';

    protected $description = 'Clean break relations morph';

    public function handle()
    {
        foreach (Morph::getModelsWithCascadeDeleteTrait() as $model) {
            $numRowsDeleted = $model->deleteMorphResidual();

            if ($numRowsDeleted > 0) {
                $this->info(sprintf(
                    'Model %s: %d rows deleted.',
                    get_class($model),
                    $numRowsDeleted
                ));
            }
        }
    }
}
