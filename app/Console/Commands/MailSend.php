<?php

namespace App\Console\Commands;

use App\Http\Timer\TimeRun;
use Illuminate\Console\Command;

class MailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MailSend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command 邮件发送';

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
        //
        TimeRun::mailSendTimer();

    }
}
