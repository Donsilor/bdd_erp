<?php

namespace addons\Finance\common\enums;

/**
 * 所属项目
 * @package common\enums
 */
class ContractTypeEnum extends \common\enums\BaseEnum
{
    const DESIGN = 1;
    const MATERIAL_EQ = 2;
    const MARKETING_BU = 3;
    const OTHER = 4;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DESIGN => "设计",
            self::MATERIAL_EQ => "材料设备",
            self::MARKETING_BU => "营销业务",
            self::OTHER => "其他",
        ];
    }
    
}