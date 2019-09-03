<?php
namespace App\Auth\Controllers;


use App\Http\Controllers\Controller;

/** 授权基类
 * Class BaseAuthController
 * @package App\Auth\Controllers
 */
class BaseAuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
}