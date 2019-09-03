<?php
/**
 * @author zt12700
 * CreateTime: 2019/4/28 11:33
 *
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Auth\Common\AjaxResponse;

class Role extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'role';
    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    public function User()
    {
        return $this->hasMany(User::class ,'role_id' ,'role_id') ;
    }

    /**
     * @author zt12700
     * CreateTime: 2019/5/7 13:34
     * @return Role[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getRoles()
    {
        return self::all();
    }

    public static function getList($data,$limit)
    {
        $query = self::query();

        if(isset($data['role_name'])){
            $query->where('role_name','like' , '%'.$data['role_name'].'%');
        }
        if(isset($data['en_name'])){
            $query->where('en_name' , 'like' , '%'.$data['en_name'].'%');
        }

        if(isset($data['status'])){
            $query->where('status',$data['status']);
        }

        $info = $query->paginate($limit);
        $count = $info->total();

        return [
            'info' => $info->items(),
            'count' => $count
        ];

    }

    /**
     * 接口搜索角色方法
     * @author zt6768
     * @param array $conditon 条件
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getInfoByRole($conditon)
    {
        $where = [];

        if (isset($conditon['role_id'])) {
            $where[] = ['role_id', '=', $conditon['role_id']];
        }

        return static::where($where)->first();
    }

    /**
     * 根据条件角色信息
     * @author zt12700
     * @param array $id 条件
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getInfoByConditon($id)
    {
        $query = self::query();
        $info = $query->with('User')->find($id);

        if(collect($info->User)->isNotEmpty()){
            return AjaxResponse::isFailure(__('auth.RoleBindings'));
        }else{
            $info->status = 2;
            $bool = $info->save();
            if($bool){
                return  AjaxResponse::isSuccess(__('auth.ShutdownSuccessful'));
            }else{
                return AjaxResponse::isFailure(__('auth.ShutdownFailure'));
            }
        }


    }

    /**
     * 启用角色
     * @author zt12700
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public static function start($id)
    {
        $info = Role::find($id);
        $info->status = 1;
        $bool = $info->save();
        if($bool){
            return  AjaxResponse::isSuccess(__('auth.EnabledSuccessfully'));
        }else{
            return AjaxResponse::isFailure(__('auth.FailedEnable'));
        }
    }

    /**
     * 插入角色
     * @author zt12700
     * @param array $data 保存数据
     * @return boolean
     */
    public static function RoleInsert($data)
    {
        if(isset($data['role_id'])){
            $model =Role::find($data['role_id']);
        }else{
            $model = new Role();
        }
        $model->role_name=$data['role_name'];
        $model->en_name=$data['en_name'];
        $model->created_at=date('Y-m-d H-i-s',time());
        $model->updated_at=date('Y-m-d H-i-s',time());
        $bool=$model->save();  //保存
        return $bool;
    }

    /**
     * 分配角色权限
     * @author zt12700
     * @param $data
     */
    public static function assignAccess($data,$role_id)
    {
        if(!empty($data)){
            $result = RolePermission::where('role_id',$role_id)->get();
            if(collect($result)->isNotEmpty()){
                $res = RolePermission::where('role_id',$role_id)->delete();
            }else{
                $res = true;
            }
            if($res){
                foreach ($data as $v){
                    $info = [
                        'role_id'=>$role_id,
                        'route_permission_id'=>$v['id'],
                        'created_at'=>date('Y-m-d H-i-s',time()),
                        'updated_at'=>date('Y-m-d H-i-s',time())
                    ];
                    $bool = RolePermission::insert($info);
                }
            }
            return $bool;

        }else{
            $result = RolePermission::where('role_id',$role_id)->get();
            if(collect($result)->isNotEmpty()){
                $res = RolePermission::where('role_id',$role_id)->delete();
            }else{
                $res = true;
            }
            return $res;
        }
    }

    /**
     * 批量角色
     * @author zt12700
     * @param array $data 保存数据
     * @return boolean
     */
    public static function batchInsert($data)
    {
        $model = new Role();
        return $model->insert($data);
    }

}