<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Timer\TimeRun;
class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:Cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command 删除导出数据';

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
        TimeRun::cleanupTimer();
    }
}
