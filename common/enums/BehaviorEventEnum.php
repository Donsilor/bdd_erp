<?php
namespace common\enums;


/**
 * 日志行为事件类型
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BehaviorEventEnum extends BaseEnum
{
    const CREATE = 1;//新增
    const UPDATE = 2;//编辑
    const DELETE = 3;//删除
    const STASUS = 4;//状态
    const SORT = 5;//排序
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::CREATE => "新增",
                self::UPDATE => "编辑",
                self::DELETE => "删除",
                self::STASUS => "状态",
                self::SORT => "排序",
        ];
    }  
    
}