<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'userloginlog';

    protected $primaryKey = 'Id';
    
	protected $fillable = [
			'Id', //       
			'UsersId', // 用户id      
			'IPAddress', // ip地址      
			'AddTime', // 创建时间      
		];
	
	/**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}

