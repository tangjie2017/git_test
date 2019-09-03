<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserPermission extends Model
{

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_permission';
    /**
     * 与模型关联的数据表主键
     *
     * @var string
     */
    protected $primaryKey = 'user_permission_id';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var int 用户类型-RMS用户
     */
    const USER_TYPE_RMS = 1;

    /**
     * @var int 用户类型-PDA用户
     */
    const USER_TYPE_PDA = 2;


    /**
     * 配置每页数
     * @author zt6535
     * @var \Illuminate\Config\Repository|mixed
     */
    private $pageSize ;

    /**
     * Container constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->pageSize = config('page.pageSize');
    }

    /**
     * 路由类型
     * @author zt6535
     * CreateTime: 2019/3/18 13:46
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::USER_TYPE_RMS => 'LMS',
            self::USER_TYPE_PDA => 'PDA',
        ];
    }

    /**
     * 根据用户id和类型获取所有权限
     * @author zt6535
     * CreateTime: 2019/3/19 17:42
     * @param $id
     * @return mixed
     */
    public function getPermissionList($id)
    {
        return UserPermission::leftJoin('route', 'user_permission.route_permission_id', '=', 'route.route_id')
            ->leftJoin('route_language', 'route.route_id', '=', 'route_language.route_id')
            ->where(['user_permission.user_id' => $id])
            ->select('route.route_id as id','parent_route_id as parentId','route_name as title')
            ->orderBy('route.sort')
            ->get()
            ->toArray();
    }

    /**
     * 编辑用户权限、添加用户日志
     * @author zt6535、zt6768
     * @param int $userId 用户id
     * @param array $userPermission 用户权限
     * @param array $userLog 用户权限日志
     * @return boolean
     */
    public static function updateRoutesByIdAndRoutes($userId, $userPermission, $userLog = array())
    {
        try {
            DB::transaction(function () use ($userId, $userPermission, $userLog) {
                 DB::table('user_permission')->where(['user_id' => $userId])->delete();
                 DB::table('user_permission')->insert($userPermission);
                if($userLog){
                    DB::table('user_log')->insert($userLog);
                }
            }, 5);
            DB::commit();

            return true;
        } catch (\PDOException $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();

            return false;
        }
    }

    /**
     * 复制用户权限
     * @author zt7239
     * @param array $BeReplicatedUser  被复制的用户
     * @param string $ReplicatedUser  复制的用户
     * @param int $user_id  当前登录的用户id
     * @return bool
     */
    public static function copyPermission($BeReplicatedUser, $ReplicatedUser, $user_id)
    {

        $copyPermission = self::where('user_id',$ReplicatedUser)->get();

        $time = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try{

            DB::table('user_permission')->whereIn('user_id',$BeReplicatedUser)->delete();
            $beCopyPermission = self::whereIn('user_id',$BeReplicatedUser)->get();
            if($beCopyPermission->isEmpty()){
                foreach($BeReplicatedUser as $value){
                    foreach ($copyPermission as $v){
                        $ins[] = [
                            'user_id' => $value,
                            'route_permission_id' => $v->route_permission,
                            'created_at' => $time,
                            'updated_at' => $time,
                        ];
                    }
                }

                $res1 = DB::table('user_permission')->insert($ins);
                if(!$res1) {
                    DB::rollback();
                    return false;
                }else{
                    $beCopyUser = DB::table('user')->whereIn('user_id',$BeReplicatedUser)->get();
                    $copyman = DB::table('user')->where('user_id',$ReplicatedUser)->first();
                    $username = DB::table('user')->where('user_id',$user_id)->first();
                    foreach($beCopyUser as $v1){
                        $insArr[] = [
                            'operator_user_id' => $username->user_id,
                            'operator_user_name' => $username->user_code,
                            'passive_user_id' => $v1->user_id,
                            'passive_user_name' => $v1->user_code,
                            'content' => $v1->user_code . __('auth.copy') . $copyman->user_code . __('auth.permission'),
                            'created_at' => $time,
                        ];
                    }
                    DB::table('user_log')->insert($insArr);
                    DB::commit();
                    return true;
                }
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
     * 获取用户绑定权限
     * @author zt6768
     * @param int $userId 用户id
     * @return array
     */
    public static function getUserPermission($userId)
    {
        return self::where('user_id', $userId)->get()->pluck('route_permission_id')->toArray();
    }

    /**
     * 给用户添加默认权限
     * @author zt6768
     * @param array $data 保存数据
     * @return boolean
     */
    public static function addDefaultUserPermission($data)
    {
        $model = new UserPermission();

        return $model->insert($data);
    }

}
