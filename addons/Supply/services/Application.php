<?php

namespace addons\Supply\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Supply\services
 * @property \addons\Supply\services\FactoryService $factory 商品分类
 * @property \addons\Supply\services\ProduceService $produce 商品分类
 *
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        /*********供应商相关*********/
        'factory' => 'addons\Supply\services\FactoryService',
        'produce' => 'addons\Supply\services\ProduceService',
    ];
}