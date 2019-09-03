<?php

namespace App\Console\Commands;

use App\Http\Api\OWMSService;
use App\Http\Api\WmsOmsApi;
use Illuminate\Console\Command;

class UserInfo extends Command
{
    /**
     * 控制台命令 signature 的名称.
     *
     * @var string
     */
    protected $signature = 'command:UserInfo';

    /**
     * 获取谷仓用户信息
     * 接口执行频率：每天执行2次，早上08::00执行，中午12:00执行
     *
     * @var string
     */
    protected $description = '获取谷仓用户信息';

    /**
     * 创建一个新的命令实例.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行控制台命令.
     *
     * @return mixed
     */
    public function handle()
    {
        echo WmsOmsApi::getWmsUserData();
    }
}
