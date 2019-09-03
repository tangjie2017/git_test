<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Self_;
use function Symfony\Component\HttpKernel\Tests\controller_func;

class RedisController extends Controller
{
    public function addCart($userId, $goodsName, $goodsId)
    {
        $hashKey = "user_" . $userId;//hash键名
        $key = $goodsId . "_" . $goodsName;//键名
        //加入
        return Redis::hIncrBy($hashKey, $key, 1);
    }


    public function cartDelOne($userId, $goodsName, $goodsId)
    {
        $hasKey = "user_" . $userId;
        $key = $goodsId . "_" . $goodsName;//键名
        return Redis::hDel($hasKey, $key);
    }

    public function cartDelAll($userId)
    {
        $hasKey = "user_" . $userId;
        return Redis::del($hasKey);
    }

    public function cartList($userId)
    {
        $hashKey = "user_" . $userId;
        return Redis::hGetAll($hashKey);
    }


    public function testRedis()
    {
        $userId = 1;
        $goodsName = '小米净化器';
        $goodsId = '2';
//        $userId=1;
//        $goodsName='小米吸尘器';
//        $goodsId = '3';
//        $add = $this->addCart($userId,$goodsName,$goodsId);
//        dd($add);
//        $del = $this->cartDelOne($userId,$goodsName,$goodsId);
//        dd($del);
        $list = $this->cartList($userId);
        dd($list);

    }

    /**
     * 冒泡排序
     * @author zt7242
     * @date 2019/8/21 13:31
     * @return array
     */
    public function bubble()
    {
$a= null;
        dd(empty($a));
//        $len = count($arr);
//        for ($i = 0; $i < $len; $i++) {
//            for ($j = $len - 1; $j > $i; $j--) {
//                if ($arr[$j] < $arr[$j - 1]) {
//                    $tem = $arr[$j];
//                    $arr[$j] = $arr[$j - 1];
//                    $arr[$j - 1] = $tem;
//                }
//            }
//        }
//      return $arr;
//        return ($this->quick_sorts($arr));
//        return ($this->select_sort($arr));
//        $file = '/app/http/controller/index.php';
//        $a = basename($file);
//        $a = dirname($file);
//        $a = pathinfo($file);
//       $a = filesize($file);
//       if(!is_dir($file)){
//            mkdir(iconv('utf-8','gbk',$file),0777,true);
//       }
//
//       if(!is_dir($file)){
//           mkdir(iconv('utf-8','gbk',$file),0777,true);
//       }

    }

    public function quick_sort($arr)
    {
//        $arr = [6, 5, 11, 4, 4, 10];
        if (count($arr) <= 1) return $arr;
        $key = $arr[0];
        $left = [];
        $right = [];
        for ($i = 1; $i < count($arr); $i++) {
            if ($arr[$i] < $key) {
                $left[] = $arr[$i];
            } else {
                $right[] = $arr[$i];
            }
        }
        $left = $this->quick_sort($left);
        $right = $this->quick_sort($right);
        return array_merge($left, array($key), $right);
    }


    public function select_sort($arr)
    {
        $len = count($arr);
        for ($i = 0; $i < $len - 1; $i++) {
            $p = $i;
            for ($j = $i + 1; $j < $len; $j++) {
                if ($arr[$p] > $arr[$j]) {
                    $p = $j;
                }
            }
            if ($p != $i) {
                $tem = $arr[$p];
                $arr[$p] = $arr[$i];
                $arr[$i] = $tem;
            }
        }
        return $arr;
    }

    public function wuxianji($arr)
    {
        $items = [];
        foreach ($arr as $value) {
            $items[$value['id']] = $value;
        }
        $tree = [];
        foreach ($items as $key => $value) {
            if (isset($items[$value['pid']])) {
                $items[$value['pid']]['son'][] = &$items[$key];
            } else {
                $tree[] = &$items[$key];
            }
        }
    }
//创建:CREATE INDEX <索引名> ON tablename (索引字段)
//修改:ALTER TABLE tablename ADD INDEX [索引名] (索引字段)
//创表指定索引:CREATE TABLE tablename([...],INDEX[索引名](索引字段))
//session.gc_maxlifetime

//    public function mystrtoupper($a)
//    {
//        $b= str_split($a,1);
//        $r = '';
//        foreach($b as $v){
//            $v = ord($v);
//            if($v>=97 && $v<=122){
//                $v-=32;
//            }
//            $r.=chr($v);
//        }
//        return $r;
//    }

//Accept:text/html
//Accept-Encoding:gzip
//Accept-Language:zh-CN
//Cache-control: no-cache
//Connection:keep-alive
//Host:
//Cookie:
//Content-Disposition:form-data;username="test"&pwd="test2"
//User-Agent:
//Referer:

//$SERVER['REMOTE_ADDR']
//$SERVER['REQUEST_URI']
//$SERVER['REMOTE_HOST']


//$url = '/wwwroot/include/page.class.php';

}