<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 路由模型
 */
class Route extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'route';

    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'route_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 路由名称多语言表
     * @author zt7239
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function route_language()
    {
        return $this->hasOne(RouteLanguage::class,'route_language_id','route_id')->select('*');
    }

    /**
     * 获取目录数据
     * @author zt7242
     * @date 2019/4/30 18:28
     * @return mixed
     */
    public static function getMenuData()
    {
        $lan = session()->get('lang') ?? "zh_CN";
        if($lan == 'zh_CN'){
            return  self::leftJoin('route_language','route.route_id','=','route_language.route_id')
                ->select('route.route_id as id','parent_route_id as parentId','route_name as title','url')
                ->orderBy('route.sort')
                ->get()
                ->toArray();
        }else{
            return  self::leftJoin('route_language','route.route_id','=','route_language.route_id')
                ->select('route.route_id as id','parent_route_id as parentId','en_name as title','url')
                ->orderBy('route.sort')
                ->get()
                ->toArray();
        }

    }


    /**
     * 通过该id获取路由信息
     * @author zt7239
     * @param int $id
     * @return mixed
     */
    public static function getMenuInfoById($id = 0)
    {

        return self::leftJoin('route_language','route.route_id','=','route_language.route_id')->where(['route.route_id'=>$id])->get()->first()->toArray();
    }


    /**
     * 新增或编辑保存数据
     * @author zt7242
     * @date 2019/5/6 11:20
     * @param $data
     * @return bool
     */
    public static function saveData($data)
    {
        $nowTime = date('Y-m-d H:i:s');
        $model = new self();

        if(isset($data['route_id'])) {
            $model = $model::find($data['route_id']);
            if(!$model){
                return ['status'=>2];//没有数据，返回错误提示
            }
            $route_id = $model->route_id;
            $pid = $model->parent_route_id;
        }else{
            $model ->created_at = $nowTime;
        }

        DB::beginTransaction();
        try{
            $model->updated_at = $nowTime;
            if(isset($pid)){
                $model->parent_route_id = $pid;
            }else{
                $model->parent_route_id = $data['parent_route_id'];
            }

            $model->url = $data['url'];
            $model->sort = $data['sort'];
            $model->save();
            $routeId = $model->route_id;
            if(isset($route_id)) {
                DB::table('route_language')->where(['route_id'=>$route_id])->update(['route_name' =>$data['route_name'],'en_name'=>$data['en_name']]);
            }else{
                DB::table('route_language')->insert(['route_name' =>$data['route_name'],'en_name'=>$data['en_name'],'route_id'=>$routeId]);
            }

            DB::commit();
            return true;
        }catch (\Exception $e ){
            Log::info($e->getMessage());
            DB::rollback();
            return false;
        }

    }

    /**
     * 删除菜单
     * @author zt7242
     * @date 2019/5/6 10:59
     * @param $strIds
     * @return bool
     */
    public static function delMenuInfo($strIds)
    {
        $arrId = explode(',',$strIds);
        DB::beginTransaction();
        try{
            $res1 = DB::table('route')->whereIn('route_id' , $arrId)->delete() ;
            $res2 = DB::table('route_language')->whereIn('route_id',$arrId)->delete();

            if($res1 && $res2){
                DB::commit();
                return true;
            }else{
                DB::rollback();
                return false;
            }

        }catch (\Exception $e){
            Log::info($e->getMessage());
            DB::rollback();
            return false;
        }


    }

    /**
     * 根据当前语言获取路由
     * @author zt7239、zt6768
     */
    public static function getAllRoute()
    {
        $lang = session()->get('lang') ? session()->get('lang') : "zh_CN";
        return self::leftJoin('route_language','route.route_id','=','route_language.route_id')->orderBy('route.route_id')->get()->toArray();
    }

    /**
     * 根据路由id获取路由
     * @author zt6768
     * @param array $routeIds 路由id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getListByRouteId($routeIds)
    {
        return self::whereIn('route_id', $routeIds)->get();
    }

    /**
     * 获取所有路由
     * @author zt6768
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getALL()
    {
        return self::all();
    }

}
