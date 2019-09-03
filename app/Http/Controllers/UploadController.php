<?php

namespace App\Http\Controllers;

use App\Auth\Common\AjaxResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * 上传拍照图片
     * @author zt6768
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function file(Request $request)
    {
        $file = $request->file('file');

        if (empty($file)) {
            return AjaxResponse::isFailure(__('auth.pleaseUploadTheFile'));
        }

        if ($file->isValid()) {
            $entension = strtolower($file->getClientOriginalExtension());
            //限制图片格式
            if (!in_array($entension, config('filesystems.format'))) {
                return AjaxResponse::isFailure(__('auth.fileFormatIsNotSupported'));
            }

            //限制大小
            $size = $file->getSize();
            if ($size > config('filesystems.limitFileSize')) {
                return AjaxResponse::isFailure(__('auth.limitFileSize'));
            }

            //文件名
            $fileName = uniqid().'.'.$entension;
            //图片上传路径
            $uploadPath = 'uploads/'.date("Y-m-d").'/';

            $movePath = $file->move($uploadPath, $fileName);
            $saveDir = $movePath->getPath();
            $savePath = '/'.$saveDir.'/'.$fileName;

            return AjaxResponse::isSuccess(null, [
                'filePath' => $savePath,
            ]);
        }

        return AjaxResponse::isFailure(__('auth.uploadFailure'));
    }

}
