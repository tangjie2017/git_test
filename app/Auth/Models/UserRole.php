<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'userrole';

    protected $primaryKey = 'Id';
    
	protected $fillable = [
			'Id', //       
			'UserId', //       
			'RoleId', //       
		];
	
	/**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}

