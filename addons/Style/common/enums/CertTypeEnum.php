<?php

namespace addons\Style\common\enums;


/**
 * 证书类型枚举
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class CertTypeEnum extends \common\enums\BaseEnum
{
    const CENT_TYPE_GIA = 'GIA';
    const CENT_TYPE_AGS = 'AGS';
    const CENT_TYPE_OTHER = '其它';
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::CENT_TYPE_GIA => 'GIA',
                self::CENT_TYPE_AGS => 'AGS',
                self::CENT_TYPE_OTHER => '其它',
        ];
    }

    
}