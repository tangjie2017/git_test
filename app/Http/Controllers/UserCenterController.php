<?php
/**
 * 用户中心
 * @author zt12700
 * CreateTime: 2019/4/28 10:03 *
 */

namespace App\Http\Controllers;

use App\Auth\Common\Response;
use App\Auth\Controllers\BaseAuthController;
use App\Models\UserWarehouse;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\RouteService;
use App\Services\UserPermissionService;
use App\Models\UserCenter;
use App\Models\Role;
use App\Auth\Common\AjaxResponse;

class UserCenterController extends BaseAuthController
{
    public function index()
    {
        $role = UserService::getRole(); //获取角色

        return view("userCenter.index",compact('role'));
    }

    //搜索
    public function search(Request $request)
    {
        $info = $request->all();

        $data = isset($info['data']) ? $info['data'] : '';

        $limit = $info['limit'];
        $list = UserCenter::getList($data,$limit);

        $res = array(
            'code' => '0',
            'msg' =>'',
            'count' => $list['count'],
            'data' => $list['info']
        );

        return Response()->json($res);
    }


    /**
     * 查看
     * @author zt12700
     * @param Request $request
     */
    public function userlook(Request $request)
    {
        $id = $request->input('user_id');
        $data = UserCenter::look($id);

        return view('userCenter.look',['data'=>$data['data'],'warehouse'=>$data['warecode']]);
    }

    /**
     * 编辑页面
     * @author zt12700
     * @param Request $request
     */
    public function edit(Request $request)
    {
        $role = UserService::getRole(); //获取角色
        $id = $request->input('id');

        $data = UserCenter::look($id);

        $role_name = Role::find($data['data']['role_id']);
        $role_name = $role_name['role_name'];
        return view('userCenter.edit',['data'=>$data['data'],'warehouse'=>$data['warecode'],'role'=>$role,'role_name'=>$role_name]);
    }

    /**
     *编辑仓库
     * @author zt12700
     * @param Request $request
     */
    public function warehouse(Request $request)
    {
        $params = $request->all();
        $res = UserCenter::edit($params);

        return $res;

    }

    /**
     * 查看权限
     * @author zt12700
     * @param int $id 用户id
     * @return \Illuminate\View\View
     */
    public function auth(Request $request)
    {
        $id = $request->input('id');
        $role_id = $request->input('role_id');

        $list = UserPermissionService::getPermissionList($id,$role_id);

        $tree = json_encode($list,true);

        return view('userCenter.auth', [
            'tree' => $tree,
            'node' => $list
        ]);
    }

    /**
     * 分配权限
     * @author zt12700
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignAccess(Request $request)
    {
        $params = $request->all();
        $user_id = $params['user_id'];
        $data = [];
        if(isset($params['info'])){
            //取出路由权限id
            foreach ($params['info'] as $v){
                $data[] = ['id' => $v['nodeId']];
            }
        }

        //重新分配角色权限
        $bool = UserCenter::assignAccess($data,$user_id);

        if($bool){
            return  AjaxResponse::isSuccess(__('auth.SuccessfulDistribution'));
        }else{
            return AjaxResponse::isFailure(__('auth.AllocationFailed'));
        }
    }


}