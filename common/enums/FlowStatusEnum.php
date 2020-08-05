<?php

namespace common\enums;

/**
 * 流程方式
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class FlowStatusEnum extends BaseEnum
{
    const GO_ON = 1;//审批中
    const COMPLETE = 2;//完成
    const CANCEL = 9;//完成

    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::GO_ON => "审批中",
                self::COMPLETE => "完成",
                self::CANCEL => "取消",

        ];
    }   

    
}