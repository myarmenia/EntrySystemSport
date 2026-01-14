<?php

namespace App\Console\Commands;

use App\Mail\SendEmailClient;
use App\Models\AttendanceSheet;
use App\Services\CronJobService;
use App\Services\ReportFilterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendEmailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendEmail:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(protected CronJobService $cronJobService)
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $data = $this->cronJobService->get();
        Log::info("email sending client successfully");

    }
}
