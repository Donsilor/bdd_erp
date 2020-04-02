<?php

namespace addons\Style\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Style\services
 * @property \addons\Style\services\StyleCateService $styleCate 商品分类
 * @property \addons\Style\services\AttributeService $attribute 商品属性
 * @property \addons\Style\services\ProductTypeService $productType 商品类型（产品线）
 * @property \addons\Style\services\DiamondService $diamond 裸钻
 * @property \addons\Style\services\DiamondSourceService $diamondSource 裸钻来源
 * @property \addons\Style\services\StyleGoodsService $styleGoods 款式商品
 * @property \addons\Style\services\StyleService $style 款式
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
        'styleGoods' => 'addons\style\services\StyleGoodsService',      
        'diamond' => 'addons\style\services\DiamondService',
        'diamondSource' => 'addons\style\services\DiamondSourceService',        
    ];
}