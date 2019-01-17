<?php

namespace Cesargb\Database\Support;

use Illuminate\Console\Command;

class MorphCleanCommand extends Command
{
    protected $signature = 'morph:clean';

    protected $description = 'Clean break relations morph';

    public function handle()
    {
        foreach (get_declared_classes() as $class) {
            if (array_key_exists(CascadeDelete::class, class_uses($class))) {
                try {
                    (new $class)->deleteMorphResidual();

                    $this->info(sprintf(
                        'Clean class %s',
                        $class
                    ));
                } catch (LogicException $e) {
                    $this->error(sprintf(
                        'Error to delete residual of class %s: %s',
                        $class,
                        $e->getMessage()
                    ));
                }
            }
        }
    }
}
