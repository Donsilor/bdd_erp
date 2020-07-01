<?php

namespace addons\Style\common\enums;


/**
 * 起版来源枚举
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class QibanSourceEnum extends \common\enums\BaseEnum
{
  const TYPE_BASE = 1;  
  const TYPE_COMBINE = 3;
  const TYPE_SALE = 2;


  /**
   * @return array
   */
  public static function getMap(): array
  {
    return [
        self::TYPE_BASE => '基础属性',        
        self::TYPE_COMBINE => '镶嵌属性',
        self::TYPE_SALE => '销售属性',
    ];
  }
   

}