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
  const TYPE_SALE = 2;
  const TYPE_MADE = 3;
  const TYPE_CATE = 4;
  const TYPE_SERVER = 5;
    
  /**
   * @return array
   */
  public static function getMap(): array
  {
    return [
        self::TYPE_BASE => '基础属性',
        self::TYPE_SALE => '销售属性',
        self::TYPE_MADE => '定制属性',
        self::TYPE_CATE => '款式分类',
    ];
  }
   
    public static function getRemarkMap(): array
    {
        return [
            self::TYPE_BASE => '基础属性（商品基本参数--信息在商品详情编辑“基础属性”模块展示）',
            self::TYPE_SALE => '销售属性（跟商品sku价格相关的属性-eg:指圈号/金属材质等）',
            self::TYPE_MADE => '定制属性（目前仅限：定制/售后模块）',
            self::TYPE_CATE => '款式分类（按照商品特性对进行分类：eg:款式，系列等）',
        ];
    }

}