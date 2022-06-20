<?php
namespace App\Enums;

abstract class Enum
{
    abstract static function descriptions():array;

    static function getDescription($value){
        $arr=static::descriptions();
        if($arr && isset($arr[$value])){
            return $arr[$value];
        }
        return null;
    }

    static function getValue($description){
        return array_search($description,static::descriptions());
    }
}
