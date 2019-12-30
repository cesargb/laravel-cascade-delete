<?php

namespace Cesargb\Database\Support\Commands;

use Cesargb\Database\Support\Helpers\Helper;
use Illuminate\Console\Command;
use LogicException;

class MorphCleanCommand extends Command
{
    protected $signature = 'morph:clean';

    protected $description = 'Clean break relations morph';

    public function handle()
    {
        foreach (Helper::getClassWithCascadeDeleteTrait() as $className) {
            try {
                (new $className)->deleteMorphResidual();

                $this->info(sprintf('Cleaned class %s', $className));
            } catch (LogicException $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
