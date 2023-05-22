<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Test;
use App\Services\Inge7\Inge7Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncInge7 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:inge7';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes tests to inge7';

    /**
     * Execute the console command.
     *
     * @param Inge7Service $service
     * @return int
     */
    public function handle(Inge7Service $service): int
    {
        $countTests = Test::count();

        $this->info("Found $countTests tests");
        $this->info("Start syncing tests to inge7");

        $bar = false;
        if ($this->output->isVerbose()) {
            $bar = $this->output->createProgressBar($countTests);
        }

        Test::whereNotNull(['bsn', 'eu_event_type'])
            ->chunk(500, function (Collection $tests) use ($service, $bar) {
                try {
                    DB::beginTransaction(); // Not needed when we don't update records

                    /** @var Test $test */
                    foreach ($tests as $test) {
                        try {
                            $service->setTest($test);

                            // Synced status not needed but we will update this
                            $test->i7_synchronised = true;
                            $test->i7_synchronised_at = Carbon::now();
                            $test->save();
                        } catch (Exception $e) {
                            $this->error('There was an error while processing test ' . $test->id);
                            $this->error($e->getMessage());
                        } finally {
                            if ($bar) {
                                $bar->advance();
                            }
                        }
                    }

                    DB::commit();
                } catch (Exception $e) {
                    $this->error('There was an error while processing a batch of tests.');
                    $this->error($e->getMessage());

                    DB::rollBack();
                }
            });

        if ($bar) {
            $bar->finish();
        }
        $this->info("\n");
        $this->info('Done syncing tests to inge7');

        return 0;
    }
}
