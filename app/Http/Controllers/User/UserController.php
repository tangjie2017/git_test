<?php
/**
 * Created by PhpStorm.
 * User: 严伟
 * Date: 2017/2/6
 * Time: 15:34
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function __invoke($id)
    {
        return view('user.profile');
    }

}