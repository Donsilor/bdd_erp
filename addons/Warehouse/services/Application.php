<?php

namespace addons\Warehouse\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Warehouse\services
 * @property \addons\Warehouse\services\WarehouseService $warehouse 仓库

 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [       
        /*********款号相关*********/
		'warehouse' => 'addons\Warehouse\services\WarehouseService',

    ];
}