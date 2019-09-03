<?php
/**
 * @author zt12700
 * CreateTime: 2019/5/6 10:04
 *
 */

namespace App\Http\Controllers;


use App\Auth\Common\Response;
use App\Auth\Common\AjaxResponse;
use App\Models\ReservationManagement;
use Illuminate\Http\Request;
use App\Services\ReservationCodeService;
use App\Services\LanguageService;
use App\Validates\ReservationCodeValidates;
use Mockery\Generator\Method;
use Validator;

class ReservationCodeController extends Controller
{
    /**
     * 首页
     * @author zt12700
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $reservation_code = $request->input('reservation_code');
        if(isset($reservation_code)){
            $reservationCode = $reservation_code;
        }else{
            $reservationCode = '';
        }
        $language = LanguageService::getAll();
        return view('reservationCode.index',['language'=>$language,'reservationCode'=>$reservationCode]);
    }

    /**
     * 用户登录状态首页
     * @author zt12700
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function UserIndex(Request $request)
    {
        $reservation_code = $request->input('reservation_code');
        if(isset($reservation_code)){
            $reservationCode = $reservation_code;
        }else{
            $reservationCode = '';
        }
        $language = LanguageService::getAll();
        return view('reservationCode.UserIndex',['language'=>$language,'reservationCode'=>$reservationCode]);
    }

    /**
     * 预约码验证
     * @author zt12700
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $code = $request->input('code');
        if($code == null){
            return AjaxResponse::isFailure(__('auth.EnterReservationCode'));
        }
        $res = ReservationCodeService::search($code);

        return $res;
    }

    /**
     * 预约码信息页面
     * @author zt12700
     * @param Request $request
     */
    public function add(Request $request){
        $yuyue_id =  $request->input('id');
        try{
            $id = decrypt($yuyue_id);
            session(['yuyueid'=>$yuyue_id]);
        }catch (\Exception $e){
            $content=$request->session()->get('content') ?? '';
            return redirect('reservation_code/index'.'?content='.$content);
        }

        $res = ReservationManagement::with('InboundOrder')->find($id);

        $language = LanguageService::getAll();
        //return $res;
        return view('reservationCode.create',['res'=>$res,'language'=>$language]);
    }


    /**
     * 预约码信息页面
     * @author zt12700
     * @param Request $request
     */
    public function UserAdd(Request $request){
        $yuyue_id =  $request->input('id');

        $id = decrypt($yuyue_id);

        $res = ReservationManagement::with('InboundOrder')->find($id);

        return view('reservationCode.UserCreate',['res'=>$res]);
    }

    /**
     * 信息修改
     * @author zt12700
     * @param Request $request
     */
    public function update(Request $request){
        $requestData = $request->all();
//       dd($requestData);
        $validator = Validator::make(
            $requestData,
            ReservationCodeValidates::getRulesAdd(),
            ReservationCodeValidates::getMessages(),
            ReservationCodeValidates::getAttributes()
        );

        if ($validator->fails()) {
            return AjaxResponse::isFailure($validator->errors()->first());
        }

        //更改数据库状态
        $bool = ReservationCodeService::updateData($request);

       return $bool;
    }


}