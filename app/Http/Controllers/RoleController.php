<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/8 14:35
 *
 */

namespace App\Http\Controllers;


use App\Auth\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Validates\RoleValidates;
use App\Auth\Common\AjaxResponse;
use Validator;
use App\Services\RolePermissionService;
use App\Services\RouteService;

class RoleController extends BaseAuthController
{
    /**
     * 角色管理首页
     * @author zt12700
     */
    public function index()
    {
        return view('role.index');
    }

    /**
     * 搜索
     * @author zt12700
     * @param Request $request
     */
    public function search(Request $request)
    {
        $info = $request->all();
        $data = isset($info['data']) ? $info['data'] : '';
        $limit = $info['limit'];
        $list = Role::getList($data,$limit);

        $res = array(
            'code' => '0',
            'msg' =>'',
            'count' => $list['count'],
            'data' => $list['info']
        );

        return Response()->json($res);

    }

    /**
     * 新增和编辑
     * @author zt12700
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAndUpdate(Request $request)
    {
        $params = $request->all();

        $id = $request->input('role_id','');

        $validator = Validator::make(
            $params,
            $id ? RoleValidates::updateRule($id) : RoleValidates::addRule(),
            RoleValidates::getMessages(),
            RoleValidates::getAttributes()

        );

        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

       $res =  Role::RoleInsert($params);

        if($res){
            return  AjaxResponse::isSuccess('保存成功');
        }else{
            return AjaxResponse::isFailure('保存失败');
        }
    }

    /**
     * 停用角色
     * @author zt12700
     */
    public function stop(Request $request)
    {
        $id = $request->input('role_id');
        $bool = Role::getInfoByConditon($id);
        return $bool;
    }

    /**
     * 启用角色
     * @author zt12700
     */
    public function start(Request $request)
    {
        $id = $request->input('role_id');
        $bool = Role::start($id);
        return $bool;
    }


    /**
     * 删除角色
     * @author zt12700
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $id = $request->input('role_id');
        $bool = Role::destroy($id);
        if($bool){
            return  AjaxResponse::isSuccess('删除成功');
        }else{
            return AjaxResponse::isFailure('删除失败');
        }
    }

    /**
     * 查看权限
     * @author zt12700
     * @param Request $request
     */
    public function giveAccess(Request $request)
    {
        $id = $request->input('id');

        $list = RolePermissionService::getPermissionList($id);
        $tree = json_encode($list,true);

        return view('role.auth', [
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
        $role_id = $params['role_id'];
        $data = [];
        if(isset($params['info'])){
            //取出路由权限id
            foreach ($params['info'] as $v){
                $data[] = ['id' => $v['nodeId']];
            }
        }

        //重新分配角色权限
        $bool = Role::assignAccess($data,$role_id);

        if($bool){
            return  AjaxResponse::isSuccess('分配成功');
        }else{
            return AjaxResponse::isFailure('分配失败');
        }
    }


}