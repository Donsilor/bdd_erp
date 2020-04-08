<?php

namespace addons\Merchants\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Merchants\services
 * @property \addons\Merchants\services\DefaultService $factory 商品分类
 *
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        /*********供应商相关*********/
        'factory' => 'addons\Merchants\services\DefaultService',
    ];
}