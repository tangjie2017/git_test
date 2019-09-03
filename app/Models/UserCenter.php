<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/28 14:16
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Auth\Common\AjaxResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserCenter extends Model
{

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user';
    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 用户绑定仓库
     * @author zt12700
     * @return $this
     */
    public function UserWarehouse()
    {
        return $this->hasMany(UserWarehouse::class ,'user_id' ,'user_id') ;
    }

    /**
     * 与角色一对多关系
     * @author zt12700
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Role()
    {
        return $this->hasOne(Role::class ,'role_id' ,'role_id') ;
    }
    /**
     * 用户搜索
     * @author zt12700
     * @param $data
     * @param $limit
     * @return array
     */
    public static function getList($data,$limit)
    {
        $query = self::query();

        if(isset($data['role_id'])){
            $query->where('role_id',$data['role_id']);
        }
        if(isset($data['user_name'])){
            $query->where('user_name' , 'like' , '%'.$data['user_name'].'%');
        }

        if (isset($data['user_code'])) {
            $query->where('user_code' , 'like' , '%'.$data['user_code'].'%');
        }

        if(isset($data['status'])){
            if($data['status']==2){
                $query->where('status',0);
            }else{
                $query->where('status',$data['status']);
            }
        }

        $info = $query->with('UserWarehouse')->with('role')->paginate($limit);

        foreach ($info->items() as $k=>  $v){
            if(!empty($v['UserWarehouse'])){
                $warehouse = [];
                foreach($v['UserWarehouse'] as $value){
                    $warehouse[] = $value['warehouse_code'];
                }
                $house = implode($warehouse,',');
                $info[$k]['userWarehouse'] =$house;
            }

        }

        $count = $info->total();

        return [
            'info' => $info->items(),
            'count' => $count
        ];
    }

    //查看
    public static function look($id)
    {
        $data = UserCenter::with('UserWarehouse')->find($id);

        //查询仓库表中所有的数据
        $res = Warehouse::pluck('wms_ware_code')->toArray();

        //用户绑定的仓库
        $info = [];
        if(isset($data['UserWarehouse'])) {
            foreach ($data['UserWarehouse'] as $v) {
                $info[] = $v['warehouse_code'];
            }
        }
        //对比之后的仓库代码
        $warecode = array_diff($res,$info);
//        dd($warecode);
        //遍历仓库代码
        $result = [];
        foreach ($warecode as $v){
            //根据仓库代码查仓库名称并拼接
           $aaa = Warehouse::where('wms_ware_code',$v)->select('warehouse_name')->get()->toArray();
            $result[] = $aaa[0]['warehouse_name']."[".$v."]";

        }

        return ['data'=>$data,'warecode'=>$result];
    }

    /**
     * 编辑
     * @author zt12700
     * @param $params
     */
    public static function edit($params)
    {
            $id = $params['id'];
            //先删除用户为$id的仓库数据
            $warehouse = isset($params['warehouse']) ? $params['warehouse'] : [];
            $res = UserWarehouse::where('user_id',$id)->get();
            if(collect($res)->isNotEmpty()){
                $result = UserWarehouse::where('user_id',$id)->delete();
            }else{
                $result = true;
            }

            if(isset($params['role_id'])){
                $user = UserCenter::find($id);
                $user->role_id =$params['role_id'];
                $user->save();
            }

            if(count($warehouse)>0){
                //将数据插入
                foreach ($warehouse as $v){
                    $result = $v;
                    $warehouse_name = substr($result,0,strpos($result,'['));
                    $warehouse_code = rtrim(substr($result,strpos($result,'[')+1),']');

                    $data = [
                        'warehouse_id'=>1,
                        'user_id' => $id,
                        'warehouse_code' =>$warehouse_code,
                        'warehouse_name' =>$warehouse_name,
                        'created_at' => date('Y-m-d H-i-s',time()),
                        'updated_at' => date('Y-m-d H-i-s',time()),
                    ];
                    $info = UserWarehouse::insert($data);
                }
                if($info){
                    return AjaxResponse::isSuccess(__('auth.EditorialSuccess'));
                }else{
                    return AjaxResponse::isFailure(__('auth.EditorFailure'));
                }
            }else{
                if($result){
                    return AjaxResponse::isSuccess(__('auth.EditorialSuccess'));
                }else{
                    return AjaxResponse::isFailure(__('auth.EditorFailure'));
                }
            }

    }

    /**
     * 分配角色权限
     * @author zt12700
     * @param $data
     */
    public static function assignAccess($data,$user_id)
    {
        if(!empty($data)){
            $result = UserPermission::where('user_id',$user_id)->get();
            if(collect($result)->isNotEmpty()){
                $res = UserPermission::where('user_id',$user_id)->delete();
            }else{
                $res = true;
            }
            if($res){
                foreach ($data as $v){
                    $info = [
                        'User_id'=>$user_id,
                        'route_permission_id'=>$v['id'],
                        'created_at'=>date('Y-m-d H-i-s',time()),
                        'updated_at'=>date('Y-m-d H-i-s',time())
                    ];
                    $bool = UserPermission::insert($info);
                }
            }
            return $bool;

        }else{
            $result = UserPermission::where('user_id',$user_id)->get();
            if(collect($result)->isNotEmpty()){
                $res = UserPermission::where('user_id',$user_id)->delete();
            }else{
                $res = true;
            }
            return $res;
        }
    }
}