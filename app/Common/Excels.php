<?php
namespace App\Common;

use Excel;

/**
 * Excels 操作
 * Class Excel
 * @package App\Common
 */
class Excels
{
    /**
     * Excel文件导出功能
     * @author zt6768
     * @param array $cellData  需要导出的数据
     * @param string $fileName 文件名
     * @param string $suffix   后缀
     */
    public static function export($cellData, $fileName = 'XX表', $suffix = 'xlsx')
    {
        Excel::create(iconv("UTF-8",'GBK',$fileName), function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $tot = count($cellData) ;
                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                    'I'     =>  12,
                    'J'     =>  12,
                    'K'     =>  12,
                    'L'     =>  12,
                    'M'     =>  12,
                ))->rows($cellData)->setFontSize(12);
                // 数据内容主题 左对齐
                $sheet->cells('A1:M'.$tot, function($cells) {
                    $cells->setAlignment('left');
                });
                ob_end_clean();
            });
        })->export($suffix);


    }

    /**
     * Excel文件保存到服务器
     * @author zt7242
     * @date 2019/4/25 9:56
     * @param $cellData
     * @param string $fileName
     * @param string $suffix
     */
    public static function store($cellData, $fileName = 'XX表', $suffix = 'xlsx')
    {
        Excel::create(iconv("UTF-8",'GBK',$fileName), function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $tot = count($cellData) ;
                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                    'I'     =>  12,
                    'J'     =>  12,
                    'K'     =>  12,
                    'L'     =>  12,
                    'M'     =>  12,
                ))->rows($cellData)->setFontSize(12);
                // 数据内容主题 左对齐
                $sheet->cells('A1:M'.$tot, function($cells) {
                    $cells->setAlignment('left');
                });
//                ob_end_clean();
            });
            //默认保存在storage/exports下，如想更改路径，直接加在第二个参数处
        })->store($suffix,'storage/exports/'.date('Ymd'));
    }
}