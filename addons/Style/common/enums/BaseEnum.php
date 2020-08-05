<?php

namespace addons\Style\common\enums;

use common\helpers\ArrayHelper;

/**
 * Class BaseEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
abstract class BaseEnum
{
    /**
     * @return array
     */
    abstract public static function getMap(): array;
    
    public static function _getMap($funcName = null)
    {
        if($funcName) {
            $map = static::$funcName();
        }else{
            $map = static::getMap();
        }
        return $map;
    }

    /**
     * @param $key
     * @return string
     */
    public static function getValue($key , $funcName = null): string
    {   
        $map = self::_getMap($funcName);
        return $map[$key] ?? '';
    }
    /**
     * @param array $keys
     * @return array
     */
    public static function getValues(array $keys, $funcName = null) : array
    {
        $map = self::_getMap($funcName);
        return ArrayHelper::filter($map, $keys);
    }
    /**
     * @return array
     */
    public static function getKeys($funcName = null): array
    {
        $map = self::_getMap($funcName);
        return array_keys($map);
    }
    
    public static function getSubMap($subname)
    {
        return self::$$subname;
    }
    
}