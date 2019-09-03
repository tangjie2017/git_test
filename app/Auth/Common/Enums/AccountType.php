<?php
namespace App\Auth\Common\Enums;


/**
 * Class 账号类型
 * @package App\Auth\Common\Enums
 */
class AccountType
{
    /**
     * 主账号
     */
    const primary = 0;

    /**
     * 子账号
     */
    const children = 1;

    /**
     * 超级管理员
     */
    const admin = 2;

    /**
     * 普通后台用户
     */
    const normal = 3;
}