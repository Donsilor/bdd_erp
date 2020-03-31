<?php

namespace services;

use common\components\Service;

/**
 * Class Application
 *
 * @package services
 * @property \services\goods\CategoryService $goodsCate 商品分类
 * @property \services\goods\AttributeService $attribute 商品属性
 * @property \services\goods\TypeService $goodsType 商品类型（产品线）
 * @property \services\goods\DiamondService $diamond 裸钻
 * @property \services\goods\DiamondSourceService $diamondSource 裸钻来源
 * @property \services\goods\GoodsService $goods 商品
 * @property \services\goods\SalepolicyService $salepolicy 销售政策
 * @property \services\goods\StyleService $style 款式
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        /*********款号相关*********/
        'goodsCate' => 'addons\style\services\CategoryService',
		'goodsType' => 'addons\style\GoodsTypeService',
        'attribute' => 'addons\style\AttributeService',                
        'style' => 'addons\style\StyleService',
        'goods' => 'addons\style\GoodsService',
        'salepolicy' => 'addons\style\SalepolicyService',        
        'diamond' => 'addons\style\DiamondService',
        'diamondSource' => 'addons\style\DiamondSourceService',        
    ];
}