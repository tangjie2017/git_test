<?php

namespace App\Services;


/**
 * 多语言服务层
 */
class LanguageService
{
    /**
     * 多语言选择
     * @author zt6768
     * @return array
     */
    public static function getAll()
    {
        return [
            'zh_CN' => '简体中文',
            'en_US' => 'English'
        ];
    }
}
