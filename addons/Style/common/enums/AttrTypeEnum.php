<?php

namespace addons\Style\common\enums;


/**
 * 属性类型枚举
 * 分类类型(1-基础属性,2-销售属性,3-定制属性,4款式分类)
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AttrTypeEnum extends \common\enums\BaseEnum
{
  const TYPE_BASE = 1;  
  const TYPE_COMBINE = 3;
  const TYPE_SALE = 2;
  
  public static $ChenpinIds = [
          self::TYPE_BASE,
          self::TYPE_COMBINE,
          self::TYPE_SALE,
  ];
  public static $KongtuoIds = [
          self::TYPE_BASE,
          self::TYPE_SALE,
  ];
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
   
  public static function getRemarkMap(): array
  {
        return [
            self::TYPE_BASE => '基础属性（商品基本参数--信息在商品详情编辑“基础属性”模块展示）',
            self::TYPE_SALE => '销售属性（跟商品sku价格相关的属性-eg:指圈号/金属材质等）',
            self::TYPE_COMBINE => '镶嵌属性（配件属性：如 石头,颜色/净度等）',
        ];
  }

}