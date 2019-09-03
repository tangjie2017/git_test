<?php

namespace App\Services;

use App\Models\Download;
use App\Models\StaticState;


class DownloadService
{


    /**
     * 获取下载列表
     * @author zt7242
     * @date 2019/4/23 14:55
     * @param $data
     * @param $limit
     * @return array
     */
    public static function getDownloadList($data,$limit)
    {
        return Download::getDownloadList($data,$limit);
    }

    /**
     * 下载删除
     * @author zt7242
     * @date 2019/4/24 11:37
     * @param $id
     * @return mixed
     */
    public static function delDownload($id)
    {
        return Download::delDownload($id);
    }

    /**
     * 获取下载信息
     * @author zt7242
     * @date 2019/4/24 14:03
     * @param $id
     * @return mixed
     */
    public static function getDownloadInfo($id)
    {
        return Download::getDownloadInfo($id);
    }


    /**
     * 将下载地址保存download表中并修改dowmload表状态，修改download_time表状态
     * @author zt7242
     * @date 2019/4/25 11:22
     * @param $filePath
     * @param int $download_id
     * @return bool
     */
    public static function updateFileAndStatus($filePath,$download_id=0)
    {
        if(empty($download_id)) return false;

        if(empty($filePath)){
            $downloadStatus = Download::STATUS_FAIL;
        }else{
            $downloadStatus = Download::STATUS_PROCESSED;
        }
        return Download::updateFileAndStatus($filePath,$download_id,$downloadStatus);


    }

    /**
     * 获取下载状态
     * @author zt7242
     * @date 2019/5/15 13:32
     * @param null $key
     * @return array|mixed
     */
    public static function getDownloadStatus($key =null)
    {
        $data = [
            StaticState::STATUS_PROCESSING => __('auth.processing'),
            StaticState::STATUS_PROCESSED => __('auth.processed'),
            StaticState::STATUS_FAIL => __('auth.fail'),
        ];

        if ($key) {
            return $data[$key];
        }

        return $data;
    }
}