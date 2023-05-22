<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanAuditLog extends Command
{
    protected const DEFAULT_DAYS = 30;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit-log:clean {--dry-run} {--days=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up old audit logs';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = (int)$this->option('days') > 0 ? (int)$this->option('days') : self::DEFAULT_DAYS;
        if ($days < 14) {
            print "Cowardly refusing to delete adit logs less than 14 days old\n\n";
            return 1;
        }

        $builder = AuditLog::where('created_at', '<', Carbon::now()->subDays($days)->toDateString());

        if ($this->option('dry-run')) {
            $this->dryRun($builder);
        } else {
            $this->removeLogs($builder);
        }

        return 0;
    }

    /**
     * @param mixed $builder
     */
    public function dryRun($builder): void
    {
        $logs = $builder->get();
        print "Would delete " . count($logs) . " log record(s)\n";
    }

    /**
     * @param mixed $builder
     */
    public function removeLogs($builder): void
    {
        $builder->delete();
    }
}
