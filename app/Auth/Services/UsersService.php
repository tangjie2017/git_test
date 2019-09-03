<?php

namespace App\Auth\Services;

use App\Auth\Common\CurrentUser;
use App\Auth\Common\Enums\AccountType;
use App\Auth\Models\UserLoginLog;
use App\Auth\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use App\Auth\Models\Users;
use App\Auth\Models\Role;
use App\Auth\Common\Response;
use App\Auth\Common\Config;
use Psr\Log\InvalidArgumentException;
use spec\Prophecy\Promise\RequiredArgumentException;


class UsersService
{
    /**
     * @param $userCode
     * @param $password
     * @param $userIp
     * @param $vcode
     * @param $sessionVCode
     * @param $requiredVCode
     * @return Response
     */
    public static function login($userCode, $password,$userIp, $vcode, $sessionVCode,& $requiredVCode){
        $limitLoginErrorTime = Config::limitLoginErrorTime();
        $limitLoginErrorNumber = Config::limitLoginErrorNumber();
        $user = Users::where(function($query) use($userCode){
            $query->where('UserCode',$userCode)->orWhere("Email",$userCode);
        })->first();
        if(empty($user) === false){
            if($user->Status == false){
                return Response::isFailure(__("auth.loginAccountUnable"));
            }

            if($limitLoginErrorTime > 0
            && $limitLoginErrorNumber > 0
            && $user->LoginErrorNumber >= $limitLoginErrorNumber
            && $user->LoginErrorTime != null
            && (time() - strtotime($user->LoginErrorTime)) < $limitLoginErrorNumber * 60){
                if(Config::requiredVerifyCode()){
                    $requiredVCode = true;
                }

                return Response::isFailure(sprintf(__("auth.loginErrorOverLimit"),$limitLoginErrorTime));
            }

            if(Config::requiredVerifyCode() && $user->LoginErrorNumber >= Config::loginErrorNumberEnableVerifyCode()){
                $requiredVCode = true;
                if(empty($vcode)){
                    return Response::isFailure(__("auth.verifyCodeRequired"));
                }

                if(strtolower($vcode) != strtolower($sessionVCode)){
                    return Response::isFailure(__("auth.verifyCodeError"));
                }
            }
        }

        if(empty($user) || Hash::check($password, $user->Password) == false){
            if(empty($user) == false){
                $user->LoginErrorNumber += 1;
                $user->LoginErrorTime = date('Y-m-d H:i:s');

                if(Config::requiredVerifyCode() && $user->LoginErrorNumber >= Config::loginErrorNumberEnableVerifyCode()){
                    $requiredVCode = true;
                }

                $user->save();
            }
            return Response::isFailure(__("auth.accountError"));
        }

        $user->LoginErrorNumber = 0;
        $user->LoginErrorTime = date('Y-m-d H:i:s');
        $user->LastLoginTime = date('Y-m-d H:i:s');
        $user->save();

        UserLoginLog::create([
            'AddTime'=>date('Y-m-d H:i:s'),
            'IPAddress'=>$userIp,
            'UsersId'=>$user->Id
        ]);

        return Response::isSuccess($user);
    }

    public static function query($condition,$rows){
        $currentUser = CurrentUser::getCurrentUser();
        $select = Users::whereRaw('1=1');
        if(empty($condition['param']) == false){
            $select = Users::where(function($query) use($condition){
                $param = '%'.$condition['param'].'%';
                $query->where("UserCode",'like',$param)->orWhere("UserName","like",$param);
            });
        }

        if($currentUser->accountType == AccountType::children){
            $select = $select->where("ParentUserId","=",$currentUser->primaryUserId);
        }
        if($currentUser->accountType == AccountType::primary){
            $select = $select->where(function($query) use($currentUser){
                $query->where("ParentUserId","=",$currentUser->userId)->orWhere("Id","=",$currentUser->userId);
            });
        }
        if(Config::isAdminSystem()){
            return $select->join("UserRole",function($join){
                $join->on("UserRole.UserId","=","Users.Id");
            })->join("Role",function($join){
                $join->on("Role.Id","=","UserRole.RoleId");
            })->orderBy("Users.Id")
            ->select(
                "Users.Id",
                "Users.UserCode",
                "Users.UserName",
                "Users.Email",
                "Users.PhoneNumber",
                "Users.TelPhone",
                "Users.Status",
                "Users.LastLoginTime",
                "Users.AddTime",
                "Role.Name as RoleName"
            )->paginate($rows);
        }else{
            $select = $select->orderBy("Users.Id")
                ->select(
                    "Id",
                    "UserCode",
                    "UserName",
                    "Email",
                    "PhoneNumber",
                    "TelPhone",
                    "Status",
                    "LastLoginTime",
                    "AddTime"
                );
            return $select->paginate($rows);
        }
    }

    public static function getById($id){
        if(Config::isAdminSystem()){
            return Users::where("Id","=",$id)->first();
        }else{
            $currentUser = CurrentUser::getCurrentUser();
            if($currentUser->accountType == AccountType::primary){
                return Users::where("Id","=",$id)->where(function($query) use($currentUser){
                    $query->where("ParentUserId","=",$currentUser->userId)->orWhere("Id","=",$currentUser->userId);
                })->first();
            }else{
                return Users::where("Id","=",$id)->where(function($query) use($currentUser){
                    $query->where("ParentUserId","=",$currentUser->primaryUserId)->orWhere("Id","=",$currentUser->primaryUserId);
                })->first();
            }
        }
    }

    public static function getByUserCode($userCode){
        return Users::where("UserCode","=",$userCode)->first();
    }

    public static function createOrUpdate($user,$roleId){
        $currentUser = CurrentUser::getCurrentUser();

        // 前台系统，不允许非前台类型用户创建或修改用户
        if(Config::isAdminSystem() == false){
            if(($currentUser->accountType == AccountType::primary || $currentUser->accountType == AccountType::children) == false){
                return Response::isFailure("你没有权限执行此操作");
            }
        }

        if(Config::isAdminSystem()){
            $role = Role::where("Id","=",$roleId)->first();
            if(empty($role)){
                return Response::isFailure("角色不存在.");
            }
        }

        if(empty($user['Id']) == false){
            $dbUser = Users::where("Id","=",$user['Id'])->first();
            if(empty($dbUser)){
                return Response::isFailure("用户不存在.");
            }

            //普通用户不允许修改超级管理员用户信息
            if($currentUser->accountType != AccountType::admin && $dbUser->AccountType == AccountType::admin){
                return Response::isFailure("你没有权限修改该用户.");
            }

            // 如果是主账号类型，允许修改当前用户与当前账号下的子账号
            if($currentUser->accountType == AccountType::primary
                && ($currentUser->userId == $dbUser->Id || $currentUser->userId == $dbUser->ParentUserId) == false){
                return Response::isFailure("你没有权限修改该用户.");
            }

            // 如果是子账号类型，允许修改当前用户与当前主账号下的子账号
            if($currentUser->accountType == AccountType::children
                && ($currentUser->userId == $dbUser->Id || $currentUser->primaryUserId == $dbUser->ParentUserId) == false){
                return Response::isFailure("你没有权限修改该用户.");
            }

            if($dbUser->PhoneNumber != $user['PhoneNumber']
                && empty(Users::where("PhoneNumber","=",$user['PhoneNumber'])->select("Id")->first()) == false){
                return Response::isFailure(sprintf(__("auth.phoneNumberUsed"),$user['PhoneNumber']));
            }

            $dbUser->PhoneNumber = $user['PhoneNumber'];
            $dbUser->UserName = $user['UserName'];
            $dbUser->TelPhone = $user['TelPhone'];
            $dbUser->Status = (int)$user['Status'];
            $dbUser->save();

            if(Config::isAdminSystem()){
                $dbUserRole = UserRole::where("UserId","=",$dbUser->Id)->first();
                if(empty($dbUserRole) == false){
                    if($dbUserRole->RoleId != $roleId){
                        $dbUserRole->RoleId = $roleId;
                        $dbUserRole->save();
                    }
                }else{
                    UserRole::create([
                        'RoleId'=>$roleId,
                        'UserId'=>$dbUser->Id
                    ]);
                }
            }
        }
        else {
            if (empty(Users::where("UserCode", "=", $user['UserCode'])->select("Id")->first()) == false) {
                return Response::isFailure(sprintf(__("auth.userCodeUsed"), $user['UserCode']));
            }

            if (empty(Users::where("Email", "=", $user['Email'])->select("Id")->first()) == false) {
                return Response::isFailure(sprintf(__("auth.emailUsed"), $user['Email']));
            }

            if (empty(Users::where("PhoneNumber", "=", $user['PhoneNumber'])->select("Id")->first()) == false) {
                return Response::isFailure(sprintf(__("auth.phoneNumberUsed"), $user['PhoneNumber']));
            }

            $dbUser = new Users();
            $dbUser->UserCode = $user['UserCode'];
            $dbUser->UserName = $user['UserName'];
            $dbUser->Email = $user['Email'];
            $dbUser->PhoneNumber = $user['PhoneNumber'];
            $dbUser->TelPhone = $user['TelPhone'];
            $dbUser->Status = (int)$user['Status'];
            $dbUser->Password = Hash::make($user['Password']);
            $dbUser->AddTime = date("Y-m-d H:i:s");
            if (Config::isAdminSystem()) {
                $dbUser->AccountType = AccountType::normal;
            } else {
                $dbUser->AccountType = AccountType::children;
                if($currentUser->accountType == AccountType::primary){
                    $dbUser->ParentUserId = $currentUser->userId;
                }else{
                    $dbUser->ParentUserId = $currentUser->primaryUserId;
                }
            }
            $dbUser->save();
            if (Config::isAdminSystem()) {
                $userRole = new UserRole();
                $userRole->RoleId = $roleId;
                $userRole->UserId = $dbUser->Id;
                $userRole->save();
            }
        }

        return Response::isSuccess();
    }

    /** 注册
     * @param array $user
     * @return Response
     * @throws \Exception
     */
    public static function register($user){
        if(empty($user)){
            throw new \Exception("user is required.");
        }

        if(empty($user['UserCode'])){
            throw new \Exception("user UserCode is required.");
        }

        if(empty($user['Email'])){
            throw new \Exception("user Email is required.");
        }

        if(empty($user['UserName'])){
            throw new \Exception("user UserName is required.");
        }

        if(empty($user['Password'])){
            throw new \Exception("user Password is required.");
        }

        if(empty($user['PhoneNumber'])){
            throw new \Exception("user PhoneNumber is required.");
        }


        if (empty(Users::where("UserCode", "=", $user['UserCode'])->select("Id")->first()) == false) {
            return Response::isFailure(sprintf(__("auth.userCodeUsed"), $user['UserCode']));
        }

        if (empty(Users::where("Email", "=", $user['Email'])->select("Id")->first()) == false) {
            return Response::isFailure(sprintf(__("auth.emailUsed"), $user['Email']));
        }

        if (empty(Users::where("PhoneNumber", "=", $user['PhoneNumber'])->select("Id")->first()) == false) {
            return Response::isFailure(sprintf(__("auth.phoneNumberUsed"), $user['PhoneNumber']));
        }

        if (Config::isAdminSystem())
        {
            throw new \Exception("后台系统不允许调用register方法.");
        }

        $dbUser = new Users();
        $dbUser->UserCode = $user['UserCode'];
        $dbUser->UserName = $user['UserName'];
        $dbUser->Email = $user['Email'];
        $dbUser->PhoneNumber = $user['PhoneNumber'];
        $dbUser->TelPhone = empty($user['TelPhone'])?"":$user['TelPhone'];
        $dbUser->Status = (int)empty($user['Status'])?0:$user['Status'];
        $dbUser->Password = Hash::make($user['Password']);
        $dbUser->AddTime = date("Y-m-d H:i:s");
        $dbUser->save();
        return Response::isSuccess($dbUser->toArray());
    }

    /** 更新密码
     * @param int $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @return Response
     */
    public static function updatePassword($userId, $oldPassword, $newPassword){
        $dbUser = Users::find($userId);
        if(empty($dbUser)){
            return Response::isFailure("用户不存在.");
        }

        if(Hash::check($oldPassword, $dbUser->Password) == false){
            return Response::isFailure(__("auth.oldPasswordError"));
        }

        $dbUser->Password = Hash::make($newPassword);
        $dbUser->save();
        return Response::isSuccess();
    }

    /** 更新用户状态
     * @param int $userId
     * @param bool $status
     * @return Response
     */
    public static function updateUserStatus($userId, $status){
        $dbUser = Users::find($userId);
        if(empty($dbUser)){
            return Response::isFailure("用户不存在.");
        }

        $dbUser->Status = $status;
        $dbUser->save();
        return Response::isSuccess();
    }
}