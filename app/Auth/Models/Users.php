<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'users';

    protected $primaryKey = 'Id';
    
	protected $fillable = [
			'Id', //       
			'UserCode', // 用户账号      
			'UserName', // 用户名称      
			'Password', // 用户密码      
			'Email', // 邮箱      
			'PhoneNumber', // 手机号      
			'TelPhone', // 联系电话      
			'AccountType', // 账号类型（0主账号，1子账号，2超级管理员，3后台普通用户）      
			'ParentUserId', // 主账号id      
			'Status', // 状态（0不可用，1可用）      
			'LoginErrorNumber', // 登陆失败次数      
			'LoginErrorTime', // 登陆失败时间      
			'LastLoginTime', // 上次登陆成功时间      
			'AddTime', // 创建时间      
		];
	
	/**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}

