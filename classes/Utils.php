<?php

class Utils
{
    public static function getRealIP(){
        if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            return  @$_SERVER['HTTP_CLIENT_IP'];
        elseif(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
            return @$_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif(filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP))
            return $_SERVER['REMOTE_ADDR'];
    }
}