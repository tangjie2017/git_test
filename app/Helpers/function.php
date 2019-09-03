<?php
if (! function_exists('convertUrlQuery')) {
    function convertUrlQuery($query)
    {
        $params = array();
        if (!$query) {
            return $params;
        }
        $queryParts = explode('&', $query);
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }
}

if (! function_exists('requestFilter')) {
    /**
     * 筛选检索字段
     * @author zt6535
     * CreateTime: 2019/3/15 13:39
     * @param null $requestArr
     * @param null $key
     * @return mixed|null
     */
    function requestFilter($requestArr = null , $key = null)
    {
        if (!$requestArr || !is_array($requestArr) || !$key) {
            return null;
        }

        if (!array_key_exists($key , $requestArr)) {
            return null;
        }

        return $requestArr[$key];
    }
}

?>