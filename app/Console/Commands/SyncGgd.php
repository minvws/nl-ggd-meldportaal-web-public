<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Test;
use App\Services\GgdService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncGgd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ggd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes tests to GGD';

    protected GgdService $ggdService;

    /**
     * @param GgdService $ggdService
     */
    public function __construct(GgdService $ggdService)
    {
        parent::__construct();

        $this->ggdService = $ggdService;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tests = Test::where('ggd_synchronised', false)->get();
        foreach ($tests as $test) {
            /** @var Test $test */

            if ($test->is_specimen) {
                print "Skipping specimen test {$test->id}... ";

                $test->ggd_synchronised = true;
                $test->ggd_synchronised_at = Carbon::now();
                $test->save();

                print "SKIP\n";

                continue;
            }

            print "Syncing test {$test->id} to GGD... ";
            if ($this->ggdService->upload($test)) {
                print "OK\n";
                $test->ggd_synchronised = true;
                $test->ggd_synchronised_at = Carbon::now();
                $test->save();
            } else {
                print "FAILED\n";
            }
        }

        return 0;
    }
}
