<?php

namespace services;

use common\components\Service;

/**
 * Class Application
 *
 * @package services
 * @property \services\goods\StyleCateService $styleCate 商品分类
 * @property \services\goods\AttributeService $attribute 商品属性
 * @property \services\goods\ProductTypeService $productType 商品类型（产品线）
 * @property \services\goods\DiamondService $diamond 裸钻
 * @property \services\goods\DiamondSourceService $diamondSource 裸钻来源
 * @property \services\goods\GoodsService $styleGoods 商品
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
        'styleCate' => 'addons\style\services\StyleCateService',
		'productType' => 'addons\style\services\ProductTypeService',
        'attribute' => 'addons\style\services\AttributeService',                
        'style' => 'addons\style\services\StyleService',
        'styleGoods' => 'addons\style\services\GoodsService',      
        'diamond' => 'addons\style\services\DiamondService',
        'diamondSource' => 'addons\style\services\DiamondSourceService',        
    ];
}