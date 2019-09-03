<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class User extends Model
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
    public $timestamps = true;

    /**
     * @var int 用户状态-禁用
     */
    const USER_DISABLED = '0';

    /**
     * @var int 用户状态-启用
     */
    const USER_ENABLED = '1';

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

    public function Role()
    {
        return $this->hasOne(Role::class ,'role_id' ,'role_id')->select('*') ;
    }

    public function UserWarehouse()
    {
        return $this->hasMany(UserWarehouse::class ,'user_id' ,'user_id')->select('*') ;
    }

    /**
     * @author zt6535
     * CreateTime: 2019/3/12 14:05
     * @param $request
     * @return mixed
     */
    public function filter($request)
    {
        $where = array();

        if(isset($request->role_id)){
            $where['user.role_id'] = $request->role_id;
        }

        if(isset($request->status)){
            $where['user.status'] = $request->status;
        }

        /*if(isset($request->warehouse)){
            $where['user_warehouse.warehouse_id'] = $request->warehouse;
        }*/

        $container = User::with('Role' );


        if(isset($request->warehouse) && $request->warehouse){
            $warehouse = $request->warehouse;
            $container = User::whereHas('UserWarehouse', function($query)use($warehouse) {
                $query->where('warehouse_id', $warehouse);
            });
        }else{
            $container = $container->with('UserWarehouse');
        }
//
//        if(isset($request->warehouse) && $request->warehouse){
//            $container = $container->whereHas('UserWarehouse' ,function ($query) use($request){
//                return $query->where('user_warehouse.warehouse_id' , $request->warehouse);
//            });
//        }

        $container->with('Role');
        $container->where($where);
        if(isset($request->user_name)){
            $container->where('user.user_name' , 'like' , '%'.$request->user_name.'%');
        }

        if (isset($request->user_code)) {
            $container->where('user.user_code' , 'like' , '%'.$request->user_code.'%');
        }

        $container = $container->orderBy('user.created_at', 'asc')->paginate($this->pageSize);

        return $container;
    }


    /**
     * 获取全部用户id
     * @author zt7242
     */
    public static function getUserId()
    {
        return self::query()->pluck('user_id');
    }
    /**
     * 用户启用状态
     * @author zt6535
     * CreateTime: 2019/3/12 13:43
     * @return array
     */
    public static function getStatus()
    {
        return [
            self::USER_DISABLED => __('auth.disabled'),
            self::USER_ENABLED => __('auth.enable'),
        ];
    }

    /**
     * 根据条件验证用户
     * @author zt6768
     * @param array $conditon 条件
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getInfoByConditon($conditon)
    {
        $where = [];

        if (isset($conditon['user_code'])) {
            $where[] = ['user_code', '=', $conditon['user_code']];
        }

        if (isset($conditon['user_id'])) {
            $where[] = ['user_id', '=', $conditon['user_id']];
        }

        return static::where($where)->first();
    }


    /**
     * 新增用户
     * @author zt6768
     * @param array $data 数据
     * @return boolean|int
     */
    public static function doCreate($data)
    {
        $model = new User();
        $model->user_id = $data['user_id'];
        $model->user_code = $data['user_code'];
        $model->password = $data['password'];
        $model->user_name = null;
        $model->role_id = $data['role_id'];
        $model->last_login_time = $data['last_login_time'];

        $bool = $model->save();
        if ($bool) {
            return $model->user_id;
        }

        return $bool;
    }


    /**
     * 批量新增用户
     * @author zt6768
     * @param array $data 保存数据
     * @return boolean
     */
    public static function batchInsert($data)
    {
        $model = new User();
        return $model->insert($data);
    }
}
