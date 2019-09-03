<?php

namespace App\Console\Commands;
use App\Http\Api\WmsSoap;
use App\Http\Api\WmsOmsApi;
use Illuminate\Console\Command;

class UserWareHouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UserWareHouse';

    /**
     * 获取用户仓库数据
     * 接口执行频率：每小时执行
     *
     * @var string
     */
    protected $description = '获取用户仓库数据';

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
        echo WmsOmsApi::getUserWarehouseData();
    }
}
