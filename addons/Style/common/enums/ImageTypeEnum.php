<?php

namespace addons\Style\common\enums;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class ImageTypeEnum extends BaseEnum
{
    const ORIGINAL = 1;
    const FACE_WORK = 2;


    /**
     * @return array
     *光面，磨砂，拉丝，光面+磨砂
     */
    public static function getMap(): array
    {
        return [
                self::ORIGINAL => "商品图片",
                self::FACE_WORK => "表面工艺",

        ];
    }

    public static function getPosition($type=null):array
    {
        $position = [];
        switch ($type){
            case self::ORIGINAL:
                $position =  ImagePositionEnum::getMap();
                break;
            case  self::FACE_WORK:
                $position = FaceWorkEnum::getMap();
                break;
            default: $position =  ImagePositionEnum::getMap() + FaceWorkEnum::getMap();
        }
        return $position;

    }
    
}