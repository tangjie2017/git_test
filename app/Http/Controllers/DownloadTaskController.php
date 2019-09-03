<?php

namespace App\Http\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Controllers\BaseAuthController;
use App\Services\DownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class DownloadTaskController extends BaseAuthController
{

    /**
     * 下载任务首页
     * @author zt7242
     * @date 2019/4/23 9:31
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $downloadStatus = DownloadService::getDownloadStatus();//获取下载状态

        return view('taskCenter.downloadTask.index',compact('downloadStatus'));
    }

    /**
     * 下载任务列表
     * @author zt7242
     * @date 2019/4/24 11:28
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadList(Request $request)
    {
        $info = $request->all();
        $data = isset($info['data']) ? $info['data'] : '';
        $limit = $info['limit'];

        $download = DownloadService::getDownloadList($data,$limit);
        $res = array(
            'code' => '0',
            'msg' =>'',
            'count' => $download['count'],
            'data' => $download['info']
        );
        return Response()->json($res);
    }

    /**
     * 下载删除
     * @author zt7242
     * @date 2019/4/24 11:39
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadDel(Request $request)
    {
        $id = $request->get('download_id' ,0) ;
        if (!$id) {
            return AjaxResponse::isFailure(__('auth.deleteFail'),'');
        }

        $re = DownloadService::delDownload($id) ;
        if (!$re) {
            return AjaxResponse::isFailure(__('auth.deleteFail'),'');
        }

        return AjaxResponse::isSuccess(__('auth.deleteSuccess'),'');
    }

    /**
     * 下载数据导出
     * @author zt7242
     * @date 2019/4/24 16:39
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExport($id)
    {
        if(empty($id)) throw (new ModelNotFoundException(__('auth.NoExportableInformation')));
        try{
            $download = DownloadService::getDownloadInfo($id);
            $url = $download->file_link;
            $name = $download->download_name;

            return response()->download(
                realpath(base_path($url)),
                $name
            );
        }catch (\Exception $e){
            log::info('点击导出异常'.$e);
            abort(404);

        }
    }
}
