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
 * @property \addons\Style\services\StyleChannelService $styleChannel 款式渠道
 * @property \addons\Style\services\StyleSourceService $styleSource 款式来源
 * @property \addons\Style\services\StyleAttributeService $styleAttribute 款和属性关系
 * @property \addons\Style\services\QibanService $qiban 起版和属性关系
 * @property \addons\Style\services\QibanAttributeService $qibanAttribute 起版和属性关系
 * @property \addons\Style\services\GoldService $gold 金料款式
 * @property \addons\Style\services\StoneService $stone 石料款式
 * @property \addons\Style\services\PartsService $parts 配件款式
 * @property \addons\Style\services\GiftService $gift 配件款式
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
        /*********款号相关*********/
        'styleCate' => 'addons\Style\services\StyleCateService',
		'productType' => 'addons\Style\services\ProductTypeService',
        'attribute' => 'addons\Style\services\AttributeService',                
        'style' => 'addons\Style\services\StyleService',
        'styleGoods' => 'addons\Style\services\StyleGoodsService',      
        'diamond' => 'addons\Style\services\DiamondService',
        'diamondSource' => 'addons\Style\services\DiamondSourceService',  
        'styleSource' => 'addons\Style\services\StyleSourceService',
        'styleChannel' => 'addons\Style\services\StyleChannelService',
        'styleAttribute' => 'addons\Style\services\StyleAttributeService',
        'qiban' => 'addons\Style\services\QibanService',
        'qibanAttribute' => 'addons\Style\services\QibanAttributeService',
        'gold' => 'addons\Style\services\GoldService',
        'stone' => 'addons\Style\services\StoneService',
        'parts' => 'addons\Style\services\PartsService',
        'gift' => 'addons\Style\services\GiftService',
    ];
}