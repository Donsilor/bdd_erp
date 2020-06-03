<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseGoodsLog;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\Url;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoodsService extends Service
{


    public function menuTabList($goods_id,$returnUrl = null)
    {
        return [
            1=>['name'=>'商品详情','url'=>Url::to(['warehouse-goods/view','id'=>$goods_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            5=>['name'=>'日志信息','url'=>Url::to(['warehouse-goods-log/index','goods_id'=>$goods_id,'tab'=>5,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * 创建货号操作日志
     * @param unknown $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseGoodsLog
     */ 
    public function createWarehouseGoodsLog($log){
        $model = new WarehouseGoodsLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }

}