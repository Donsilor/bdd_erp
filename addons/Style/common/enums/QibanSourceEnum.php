<?php

namespace addons\Style\common\enums;


/**
 * 起版来源枚举
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class QibanSourceEnum extends \common\enums\BaseEnum
{
  const MANUAL_CREATE = 1;
  const BUSINESS_APPLI = 2;
  const OTHER = 3;


  /**
   * @return array
   */
  public static function getMap(): array
  {
    return [
        self::MANUAL_CREATE => '手动创建',
        self::BUSINESS_APPLI => '业务申请',
        self::OTHER => '其他',
    ];
  }
   

}