<?php

namespace addons\Report\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Report\services
 * @property \addons\Report\services\CateSalesService $cateSales 产品销量
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
        'cateSales' => 'addons\Report\services\CateSalesService',
    ];
}