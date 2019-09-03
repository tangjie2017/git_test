<?php

namespace App\Common;

class Aes {

    private $hex_iv = '00000000000000000000000000000000';

    /**算法,另外还有192和256两种长度*/
    const CIPHER = MCRYPT_RIJNDAEL_128;

    /**模式*/
    const MODE = MCRYPT_MODE_CBC;

    /**加密KEY*/
    private $key;

    function __construct($key)
    {
        $this->key = hash('sha256', $key, true);
    }

    /**
     * 加密
     */
    function encrypt($str)
    {
        //打开算法和模式对应的模块
        $td = mcrypt_module_open(self::CIPHER, '', self::MODE, '');
        //初始化加密所需的缓冲区
        mcrypt_generic_init($td, $this->key, $this->hexToStr($this->hex_iv));
        //获得加密算法的分组大小
        $block = mcrypt_get_block_size(self::CIPHER, self::MODE);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        //加密数据
        $encrypted = mcrypt_generic($td, $str);
        //对加密模块进行清理工作
        mcrypt_generic_deinit($td);
        //关闭加密模块
        mcrypt_module_close($td);
        return base64_encode($encrypted);
    }

    /**
     * 解密
     */
    function decrypt($code)
    {
        //打开算法和模式对应的模块
        $td = mcrypt_module_open(self::CIPHER, '', self::MODE, '');
        //初始化加密所需的缓冲区
        mcrypt_generic_init($td, $this->key, $this->hexToStr($this->hex_iv));
        //加密数据
        $str = mdecrypt_generic($td, base64_decode($code));
        //获得加密算法的分组大小
        $block = mcrypt_get_block_size(self::CIPHER, self::MODE);
        //对加密模块进行清理工作
        mcrypt_generic_deinit($td);
        //关闭加密模块
        mcrypt_module_close($td);
        return $this->strippadding($str);
    }

    private function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    /**
     * 密钥
     */
    public static function getDefaultKey()
    {
        return config('api.wmsOms.secretKey');
    }

    function hexToStr($hex)
    {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2)
        {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }
}