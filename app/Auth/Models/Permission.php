<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'permission';

    protected $primaryKey = 'Id';

	protected $fillable = [
			'Id', //       
			'ParentId', //       
			'Name', // 权限名称      
			'ResourceName', // 权限资源名（用于多语言）      
			'Url', //       
			'PermissionType', // 0菜单，1权限      
			'PermissionSort', // 排序      
			'Icon', // 图标      
		];
	
	/**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}

