<?php

if (!function_exists('checkEmptyParam')) {
    function checkEmptyParam($params = []) {
        $error_param = [];
        foreach ($params as $param) {
            $error_param[] = $param;
        }

        return $error_param;
    }
}