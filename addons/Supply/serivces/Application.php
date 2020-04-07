<?php

namespace addons\Supply\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Supply\services
 * @property \addons\Supply\services\StyleCateService $styleCate 商品分类
 *
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
            /*********工厂模块*********/
            'factory' => 'addons\Supply\services\FactoryService',            
    ];
}