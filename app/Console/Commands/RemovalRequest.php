<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Test;
use App\Services\Inge7\Inge7Service;
use Illuminate\Console\Command;

class RemovalRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:removal {bsn} {dob}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove tests for a specific BSN and DOB';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Inge7Service $inge7Service)
    {
        $bsn = $this->argument('bsn');
        $dob = $this->argument('dob');

        $found = false;

        Test::chunk(100, function ($tests) use ($bsn, $dob, &$found, $inge7Service) {
            foreach ($tests as $test) {
                if ($test->bsn === $bsn && $test->birthdate === $dob) {
                    $this->info('Deleting test for BSN ' . $bsn . ' and DOB ' . $dob . '...');

                    $inge7Service->unsetTest($test);
                    $test->delete();

                    $found = true;
                }
            }
        });

        return $found ? 0 : 1;
    }
}
