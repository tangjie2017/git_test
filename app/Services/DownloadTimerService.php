<?php

namespace App\Services;

use App\Models\Download;
use App\Models\DownloadTimer;
use App\Models\StaticState;


class DownloadTimerService
{

    /**
     * 获取下载定时器中未执行的数据
     * @author zt7242
     * @date 2019/4/24 16:57
     * @param int $limit
     * @return mixed
     */
    public static function getDownloadTimerInfo($limit=20)
    {
        return DownloadTimer::getDownloadTimerInfo($limit);
    }


}