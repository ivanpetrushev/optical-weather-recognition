<?php

namespace App\Console\Commands;

use App\Services\HistogramService;
use Illuminate\Console\Command;

class ImportHistograms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:histograms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importing histograms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        print "Starting histogram import...\n";
        $service = new HistogramService();
        $service->createSomeHistograms();
    }
}
