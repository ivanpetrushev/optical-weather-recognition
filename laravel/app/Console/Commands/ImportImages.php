<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CrawlerService;

class ImportImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import camera images';

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
        $crawler = new CrawlerService();
        $crawler->setRootDir('/weather_data');
        $crawler->createLocations();
        $crawler->createCameras();
        echo "import ended\n";
    }
}
