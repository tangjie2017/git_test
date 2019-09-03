<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute 必须同意.',
    'active_url'           => ':attribute 是一个无效的URL.',
    'after'                => ':attribute 必须大于日期:date.',
    'after_or_equal'       => ':attribute 必须大于或等于日期:date.',
    'alpha'                => ':attribute 只能包含字母.',
    'alpha_dash'           => ':attribute 只能包含字母、数字、横杠.',
    'alpha_num'            => ':attribute 只能包含字母、数字.',
    'array'                => ':attribute 必须是一个数组.',
    'before'               => ':attribute 必须小于日期:date.',
    'before_or_equal'      => ':attribute 必须小于或等于日期:date.',
    'between'              => [
        'numeric' => ':attribute 必须在:min到:max之间.',
        'file'    => ':attribute 必须在:min到:max KB之间.',
        'string'  => ':attribute 必须在:min到:max个字符之间.',
        'array'   => ':attribute 必须包含:min到:max个明细.',
    ],
    'boolean'              => ':attribute 必须是true或false.',
    'confirmed'            => ':attribute confirmation does not match.',
    'date'                 => ':attribute 不是一个有效的日期.',
    'date_format'          => ':attribute 格式必须是:format.',
    'different'            => ':attribute 与 :other 必须是不同的.',
    'digits'               => ':attribute 必须是一个数字.',
    'digits_between'       => ':attribute 必须是一个数字，并且在:min到:max之间.',
    'dimensions'           => ':attribute 图片尺寸无效.',
    'distinct'             => ':attribute 存在重复的值.',
    'email'                => ':attribute 必须是一个有效的邮箱地址.',
    'exists'               => '选择的 :attribute 无效.',
    'file'                 => ':attribute 必须是一个文件.',
    'filled'               => ':attribute 不能为空.',
    'image'                => ':attribute 必须是一个图片.',
    'in'                   => '选择的 :attribute 无效.',
    'in_array'             => ':attribute 必须包含在 :other.',
    'integer'              => ':attribute 必须是一个整数.',
    'ip'                   => ':attribute 必须是一个有效的IP地址.',
    'json'                 => ':attribute 必须是一个有效的JSON字符串.',
    'max'                  => [
        'numeric' => ':attribute 不能大于:max.',
        'file'    => ':attribute 不能大于:max KB.',
        'string'  => ':attribute 不能大于:max 个字符.',
        'array'   => ':attribute 不能大于:max 个明细.',
    ],
    'mimes'                => ':attribute 文件类型必须是: :values.',
    'mimetypes'            => ':attribute 文件类型必须是: :values.',
    'min'                  => [
        'numeric' => ':attribute 不能小于:min.',
        'file'    => ':attribute 不能小于:min KB.',
        'string'  => ':attribute 不能小于:min 个字符.',
        'array'   => ':attribute 不能小于:min 个明细.',
    ],
    'not_in'               => '选择的 :attribute 是无效的.',
    'numeric'              => ':attribute 必须是一个数字.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => ':attribute 格式无效.',
    'required'             => ':attribute 不能为空.',
    'required_if'          => '当:other 为 :value时，:attribute 不能为空.',
    'required_unless'      => '当:other 不为 :values时，:attribute 不能为空.',
    'required_with'        => ':attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => ':attribute 与 :other 必须匹配.',
    'size'                 => [
        'numeric' => ':attribute 必须是:size.',
        'file'    => ':attribute 必须是:size KB.',
        'string'  => ':attribute 必须是 :size 个字符.',
        'array'   => ':attribute 必须包含:size 个明细.',
    ],
    'string'               => ':attribute 必须是一个字符串.',
    'timezone'             => ':attribute 必须是一个有效的时区.',
    'unique'               => ':attribute 必须唯一.',
    'uploaded'             => ':attribute failed to upload.',
    'url'                  => ':attribute 格式错误.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
