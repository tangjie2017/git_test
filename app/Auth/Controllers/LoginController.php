<?php
namespace App\Auth\Controllers;

use App\Auth\Common\AjaxResponse;
use App\Auth\Common\CurrentUser;
use App\Common\Aes;
use App\Curl\Curl;
use App\Http\Controllers\Controller;
use App\Models\UserCenter;
use App\Services\LanguageService;
use App\Services\RouteService;
use App\Services\UserPermissionService;
use App\Services\UserWarehouseService;
use App\Services\UserService;
use App\User;
use Dompdf\Exception;
use Illuminate\Http\Request;
use App\Auth\Common\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use App\Models\UserWarehouse;
use App\Http\Api\Soap\SvcCall;

/**
 * 登录控制器
 * Class LoginController
 * @package App\Auth\Controllers
 */
class LoginController extends Controller
{
    /**
     * RMS登录页面
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        //判断是否有授权码
        $auth_code = $request->input('AuthCode');
        if(empty($auth_code)){
            $currentUser = CurrentUser::getCurrentUser();
            if ($currentUser) {
                return redirect('/');
            }
            $language = LanguageService::getAll();

            return view("Login.bisIndex",['language'=>$language]);
        }else {
            try {
                //通过appid和authCode换区access_token
                $appId = config('api.fbg.appId');
                $auth_code = $request->input('AuthCode');
                //语言包注册
                $language = $request->input('LANGUAGE');
                if ($language) {
                    if ($language == 'zh-CN') {
                        $language = 'zh_CN';
                        App::setLocale('zh_CN');
                    } else {
                        $language = 'en_US';
                        App::setLocale('en_US');
                    }
                    $request->session()->put('lang', $language);

                }

                $curl = new Curl();
                $requestParam = [
                    'AppId' => $appId,
                    'AuthCode' => $auth_code
                ];
                //请求auth接口
                $url = config('api.fbg.testAuth');
                $res = $curl->vpost($url, $requestParam);

                $data = json_decode($res['data'], true);
                if (substr($res['code'], 0, 1) == 2 && $data['ResponseData'] != null) {
                    session(['loginRes' => $res]);
                } else {
                    Log::error($res);
                    abort(400, $data['ResponseError']['Message']);
                }
                $result = $this->fbgLogin($res);
                if ($result == '/') {
                    return redirect('/');
                } elseif ($result == 'OperationWarehouse') {
                    return __('auth.OperationWarehouse');
                } elseif ($result == 'error') {
                    return redirect(config('api.fbg.login'));
                } else {
                    $language = LanguageService::getAll();
                    return view('Login.index', ['userCode' => $result, 'language' => $language]);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return redirect(config('api.fbg.login'));
            }

        }

    }

    /**
     * PDA登录页面
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function pdaLogin(Request $request)
    {
        //判断是否有授权码
        $auth_code = $request->input('AuthCode');
        if(empty($auth_code)){
            $currentUser = CurrentUser::getCurrentUser();
            if ($currentUser) {
                return redirect('/');
            }
            $language = LanguageService::getAll();

            return view("Login.bisPda",['language'=>$language]);
        }else {
            try {
                //通过appid和authCode换区access_token
                $appId = config('api.fbg.pdaAppId');
                $auth_code = $request->input('AuthCode');
                if (empty($auth_code)) {
                    return redirect(config('api.fbg.pdaLogin'));
                }
                //语言包注册
                $language = $request->input('LANGUAGE');
                if ($language) {
                    if ($language == 'zh-CN') {
                        $language = 'zh_CN';
                        App::setLocale('zh_CN');
                    } else {
                        $language = 'en_US';
                        App::setLocale('en_US');
                    }
                    $request->session()->put('lang', $language);

                }

                $curl = new Curl();
                $requestParam = [
                    'AppId' => $appId,
                    'AuthCode' => $auth_code
                ];
                //请求auth接口
                $url = config('api.fbg.testAuth');
                $res = $curl->vpost($url, $requestParam);

                $data = json_decode($res['data'], true);
                if (substr($res['code'], 0, 1) == 2 && $data['ResponseData'] != null) {
                    session(['loginRes' => $res]);
                } else {
                    Log::error($res);
                    abort(400, $data['ResponseError']['Message']);
//                echo $data['ResponseError']['Message'];
//                $history = config('api.fbg.pdaLogin');
//                header("refresh:3;url={$history}");
                }
                $result = $this->fbgLogin($res);
                if ($result == '/') {
                    return redirect('/');
                } elseif ($result == 'OperationWarehouse') {
                    return __('auth.OperationWarehouse');
                } elseif ($result == 'error') {
                    return redirect(config('api.fbg.login'));
                } else {
                    $language = LanguageService::getAll();
                    return view('Login.pda', ['userCode' => $result, 'language' => $language]);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return redirect(config('api.fbg.pdaLogin'));
            }
        }
    }

    /*
   * 登录
   */
    public function doLogin(Request $request){
        $userCode = $request->userCode;
        $password = $request->password;
        $redirect = $request->redirect;

        if (empty($userCode)) {
            return AjaxResponse::isFailure(__('auth.userCodeEmpty'));
        }

        if (empty($password)) {
            return AjaxResponse::isFailure(__('auth.passwordEmpty'));
        }

        //加密
        $aes = new Aes(Aes::getDefaultKey());
        $requestData = [];
        $requestData['request_ip'] =  $request->getClientIp();
        $requestData['user_code'] = $aes->encrypt($userCode);
        $requestData['user_password'] = $aes->encrypt($password);

        $curl = new Curl();
        $guid = $curl->create_guid();


        if (config('api.app_model')) {
            $url = config('api.wmsOms.userLogin');
        } else {
            $url = config('api.wmsOms.userLogin');
        }

        $time = time();
        $requestParam = [];

        $requestParam['request_id'] = $guid;
        $requestParam['request_time'] = $time;
        $requestParam['language'] = session()->get('lang') != null ? session()->get('lang') : 'zh_CN';
        $requestParam['app_code'] = 'bis';
        $requestParam['sign'] = $curl->signWms($time,$requestData);
        $requestParam['request_data'] = $requestData;

        //验证登录
        $result = $curl->vpost($url, $requestParam);

        //接口请求通过
        if (substr($result['code'],0,1) == 2) {
            $data = json_decode($result['data'], true);

            //账号或密码验证失败
            if ($data['data']['state'] != 1) {
                $logs = [];
                $logs['requestData'] = $requestParam;
                $logs['responseData'] = $result;
                Log::error($logs);

                return AjaxResponse::isFailure($data['message']);
            }

            $condition = [];
            $condition['user_code'] = $userCode;
            $user = UserService::getInfoByConditon($condition);

            //若用户不在体系下，则新增该用户
            if (empty($user)) {
                $saveUser = [];
                $saveUser['user_id'] = $data['data']['user_id'];
                $saveUser['user_code'] = $userCode;
                $saveUser['password'] = Hash::make($password);
                $saveUser['user_name'] = null;
                $saveUser['role_id'] = null;
                $saveUser['last_login_time'] = date('Y-m-d H:i:s');
                $userId = UserService::doCreate($saveUser);

                $defaultPermission = UserPermissionService::getDefaultPermission();
                //添加默认权限
                UserPermissionService::addDefaultUserPermission($userId, $defaultPermission);

                $userPermission = RouteService::routeDateKeyToValue($defaultPermission);
            } else {
                $userId = $user->user_id;
                $lastLoginTime = $user->last_login_time;
                $userStatus = $user->status;
                if ($userStatus != 1) {
                    return AjaxResponse::isFailure(__('auth.loginAccountUnable'));
                }

                //获取用户权限
                $userPermission = UserPermissionService::getUserPermission($userId);

                //如果用户没有权限并且第一次登录，则添加默认权限
                if (empty($userPermission) && empty($lastLoginTime)) {
                    $defaultPermission = UserPermissionService::getDefaultPermission();
                    //添加默认权限
                    UserPermissionService::addDefaultUserPermission($userId, $defaultPermission);

                    $userPermission = $defaultPermission;
                }

                $userPermission = RouteService::routeDateKeyToValue($userPermission);

                //更新最后登录时间
                $user->last_login_time = date('Y-m-d H:i:s');
                $user->save();
            }

            //获取用户绑定仓库时区
            $warehousetime = UserWarehouseService::warehouse($userId);
            if ($warehousetime == 8) {
                return AjaxResponse::isFailure(__('auth.OperationWarehouse'));
            }
            if ($warehousetime == 'more') {
                return AjaxResponse::isFailure(__('auth.SelectWarehouse'));
            }


            //登录操作
            $currentUser = new CurrentUser();
            $currentUser->userId = $userId;
            $currentUser->userCode = $userCode;
            $currentUser->wareTime = $warehousetime;
            $currentUser->wareTimeNotUpdate = $warehousetime;
            $currentUser->userName = null;
            $currentUser->userPermissions = $userPermission;
            $currentUser->allPermissions = RouteService::getRouteUrl();
            CurrentUser::setCurrentUser($currentUser);

            if (empty($redirect)) {
                $redirect = '/';
            }

            return AjaxResponse::isSuccess(__('auth.loginSuccess'), ['redirect' => $redirect]);
        }

        //验证用户失败，记录日志
        $logs = [];
        $logs['requestData'] = $requestParam;
        $logs['responseData'] = $result;
        Log::error($logs);

        return AjaxResponse::isFailure($result['error']);

    }



    /*
     * fbg登录
     * zt12700
     */
    public function fbgLogin($res=[])
    {
        $res =  isset($res) ? $res : session()->get('loginRes');
        //接口请求通过
        if (substr($res['code'],0,1) == 2) {
            $data = json_decode($res['data'], true);

            $userId = $data['ResponseData']['UserId'];
            $userCode = $data['ResponseData']['UserCode'];
            $condition = [];
            $condition['user_code'] = $userCode;
            $user = UserService::getInfoByConditon($condition);

            //若用户不在体系下，则新增该用户
            if (empty($user)) {
                $saveUser = [];
                $saveUser['user_id'] =$userId;
                $saveUser['user_code'] = $userCode;
                $saveUser['password'] = Hash::make($userCode);
                $saveUser['user_name'] = null;
                $saveUser['role_id'] = null;
                $saveUser['last_login_time'] = date('Y-m-d H:i:s');
                $userId = UserService::doCreate($saveUser);

                $defaultPermission = UserPermissionService::getDefaultPermission();
                //添加默认权限
                UserPermissionService::addDefaultUserPermission($userId, $defaultPermission);

                $userPermission = RouteService::routeDateKeyToValue($defaultPermission);

            } else {
                $userId = $user->user_id;
                $lastLoginTime = $user->last_login_time;

                //获取用户权限
                $userPermission = UserPermissionService::getUserPermission($userId);

                //如果用户没有权限并且第一次登录，则添加默认权限
                if (empty($userPermission) && empty($lastLoginTime)) {
                    $defaultPermission = UserPermissionService::getDefaultPermission();
                    //添加默认权限
                    UserPermissionService::addDefaultUserPermission($userId, $defaultPermission);

                    $userPermission = $defaultPermission;
                }

                $userPermission = RouteService::routeDateKeyToValue($userPermission);

                //更新最后登录时间
                $user->last_login_time = date('Y-m-d H:i:s');
                $user->save();
            }

            //获取用户绑定仓库时区
            $warehousetime = UserWarehouseService::warehouse($userId);
            if ($warehousetime == 8) {
                return 'OperationWarehouse';
            }

            if ($warehousetime == 'more') {
                return $userCode;
            }


            //登录操作
            $currentUser = new CurrentUser();
            $currentUser->userId = $userId;
            $currentUser->userCode = $userCode;
            $currentUser->wareTime = $warehousetime;
            $currentUser->wareTimeNotUpdate = $warehousetime;
            $currentUser->userName = null;
            $currentUser->userPermissions = $userPermission;
            $currentUser->allPermissions = RouteService::getRouteUrl();
            CurrentUser::setCurrentUser($currentUser);

            return $redirect = '/';
        }

        //验证用户失败，记录日志
        $logs = [];
        $logs['responseData'] = $res;
        Log::error($logs);

        return 'error';

    }


    /**
     * 验证码
     * @param Request $request
     */
    public function verifyCode(Request $request)
    {
        header("Content-type: image/jpeg");
        $captcha = new CaptchaBuilder;
        $captcha ->build()->output();
        $request->session()->flash($this->verifyCodeSessionKey, $captcha->getPhrase());
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        CurrentUser::removeCurrentUser($request);
        //return redirect('https://sbx-fbg.eminxing.com/login');
        $redirect = $request->redirect;

        if (empty($redirect)) {
            return redirect("/login");
        }

        return redirect($redirect);
    }

    /**
     * 选择仓库
     * @author zt12700
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bisLogin(Request $request)
    {
        $userCode = $request->userCode;
        $wareCode = $request->wareCode;
        $redirect = $request->redirect;

        if ($wareCode== 'USEA'){
            $timeZone = -12;
        }elseif($wareCode== 'USWE'){
            $timeZone = -15;
        }

        $user= UserCenter::where('user_code',$userCode)->first();
        $userId = $user['user_id'];
        $userPermission = UserPermissionService::getUserPermission($userId);

        //登录操作
        $currentUser = new CurrentUser();
        $currentUser->userId = $userId;
        $currentUser->userCode = $userCode;
        $currentUser->wareTime = $timeZone;
        $currentUser->wareTimeNotUpdate = $timeZone;
        $currentUser->userName = null;
        $currentUser->userPermissions = $userPermission;
        $currentUser->allPermissions = RouteService::getRouteUrl();
        CurrentUser::setCurrentUser($currentUser);

        if (empty($redirect)) {
            $redirect = '/';
        }

        return AjaxResponse::isSuccess(__('auth.loginSuccess'), ['redirect' => $redirect]);
    }

}
