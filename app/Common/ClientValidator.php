<?php
namespace App\Common;

use Validator;

/** 客户端数据验证（依赖jquery.validate.js）
 * Class ClientValidator
 * @package App\Common
 */
class ClientValidator
{
    /** 生成验证错误时提示消息
     * @param $field
     * @return string
     */
    public static function renderValidationMessage($field){
        return '<span class="field-validation-valid" data-valmsg-for="'.$field.'" data-valmsg-replace="true"></span>';
    }

    /** 生成验证代码
     * @param $field 字段（为哪个字段生成验证代码）
     * @param $rules 验证规则
     * @param $message 提示的消息
     * @param array $customeAttribute 字段名称（将多语言validation文件中的:attribute替换为该字段名称）
     * @return string 返回生成的html代码
     */
    public static function renderValidation($field, $rules, $message, $customeAttribute=[]){
        $validator = Validator::make([],$rules,$message,$customeAttribute);
        $rules = $validator->getRules()[$field];
        $rules = self::parseRules($rules);
        $jqueryValidate = 'data-val="true" ';
        $fieldName = empty($customeAttribute)||empty($customeAttribute[$field])?$field:$customeAttribute[$field];
        foreach ($rules as $r => $parameter) {
            switch ($r){
                case "required":
                    $m = (empty($message[$field.'.required'])?str_replace_first(':attribute',$fieldName, __("validation.required")):  $message[$field.'required']);
                    $jqueryValidate = $jqueryValidate."data-val-required='".self::replace($m)."'";
                    break;
                case "min":
                    if(empty($message[$field.'.min'])){
                        $m = str_replace_first(':attribute',$fieldName, __("validation.min")['string']);
                        $m = str_replace_first(':min',$parameter,$m);
                    }else{
                        $m = $message[$field.'.min'];
                    }

                    $jqueryValidate = $jqueryValidate."data-val-length-min='".$parameter."'"
                        ." data-val-length='".self::replace($m)."'";
                    break;
                case "max":
                    if(empty($message[$field.'.max'])){
                        $m = str_replace_first(':attribute',$fieldName, __("validation.max")['string']);
                        $m = str_replace_first(':max',$parameter,$m);
                    }else{
                        $m = $message[$field.'.max'];
                    }

                    $jqueryValidate = $jqueryValidate."data-val-length-max='".$parameter."'"
                        ." data-val-length='".$m."'";
                    break;
                case "between":
                    if(empty($message[$field.'.between'])){
                        $m = str_replace_first(':attribute',$fieldName, __("validation.between")['string']);
                        $m = str_replace_first(':min',$parameter[0],$m);
                        $m = str_replace_first(':max',$parameter[1],$m);
                    }else{
                        $m = $message[$field.'.minmax'];
                    }

                    $jqueryValidate = $jqueryValidate."data-val-length-min='".$parameter[0]."'"
                        ." data-val-length-max='".$parameter[1]."'"
                        ." data-val-length='".self::replace($m)."'";
                    break;
                case "regex":
                    if(empty($message[$field.'.regex'])){
                        $m = str_replace_first(':attribute',$fieldName, __("validation.regex"));
                    }else{
                        $m = $message[$field.'.regex'];
                    }
                    $parameter = str_replace_first("/","",$parameter);
                    $parameter = str_replace_last("/","",$parameter);
                    $jqueryValidate = $jqueryValidate."data-val-regex-pattern='".self::replace($parameter)."'"
                        ." data-val-regex='".self::replace($m)."'";
                    break;
            }
        }

        return $jqueryValidate;
    }

    /** 替换'字符
     * @param $str
     * @return mixed
     */
    private static function replace($str){
        return str_replace("'","&#39;",$str);
    }

    /** 解析验证规则
     * @param $rules
     * @return array
     */
    private static function parseRules($rules){
        $parsedRules = [];
        foreach ($rules as $r) {
            $parameter = '';
            if (strpos($r, ':') !== false) {
                list($r, $parameter) = explode(':', $r, 2);
            }

            // 如果是min、max规则，则进行合并
            switch ($r){
                case 'between':
                    if (strpos($parameter, ',') !== false) {
                        $parameter = explode(',', $parameter, 3);
                    }
                    break;
            }

            $parsedRules[$r] = $parameter;
        }

        return $parsedRules;
    }
}