<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseGoodsLog;
use common\components\Service;
use common\helpers\Url;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Style\common\enums\StyleSexEnum;


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
    /**
     * 创建商品编号
     * @param WarehouseGoods $model
     * @param boolean $save
     * @throws \Exception
     * @return string
     */
    public function createGoodsId($model, $save = true) {
        
        if(!$model->id) {
            throw new \Exception("编货号失败：id不能为空");
        }
        //1.供应商简称
        $supplier_tag = $model->supplier->supplier_tag ?? '00';
        $prefix   = $supplier_tag;
        //2.商品材质（产品线）
        $type_tag = $model->productType->tag ?? '0';
        $prefix .= $type_tag;
        //3.产品分类
        $cate_tag = $model->styleCate->tag ?? '';
        if(count($cate_tag_list = explode("|", $cate_tag)) < 2 ) {
            $cate_tag_list = [0,0];
        }
        list($cate_m, $cate_w) = $cate_tag_list;
        if($model->style_sex == StyleSexEnum::MAN) {
            $prefix .= $cate_m;
        } else {
            $prefix .= $cate_w;
        }
        //4.数字部分
        $middle = str_pad($model->id,8,'0',STR_PAD_LEFT);
        $model->goods_id = $prefix.$middle;
        if($save === true) {
            $result = $model->save(true,['id','goods_id']);var_dump($result);
            if($result === false){
                throw new \Exception("编货号失败：保存货号失败");
            }
        }
        return $model->goods_id;
    }

}