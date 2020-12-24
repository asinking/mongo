<?php
namespace asinking\mongo;

class Str
{

    public static function isEmpty($str)
    {
        if (empty($str) && $str !== 0 && $str !== '0')
            return true;
        else
            return false;
    }

}