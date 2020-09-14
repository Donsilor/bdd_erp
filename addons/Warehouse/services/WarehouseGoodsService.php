<?php

namespace addons\Warehouse\services;

use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseGoodsLog;
use addons\Style\common\enums\StyleSexEnum;
use common\enums\AuditStatusEnum;

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
     * @param string $log
     * @throws \Exception
     * @return object
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
        if(count($cate_tag_list = explode("-", $cate_tag)) < 2 ) {
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
            $result = $model->save(true,['id','goods_id']);
            if($result === false){
                throw new \Exception("编货号失败：保存货号失败");
            }
        }
        return $model->goods_id;
    }

    //可编辑状态
    public function editStatus($model)
    {
        return $model->is_apply == 0 || ($model->apply_id == \Yii::$app->user->identity->getId() && $model->audit_status == AuditStatusEnum::SAVE);
    }

    //可提审状态
    public function applyStatus($model){
        return $model->apply_id == \Yii::$app->user->identity->getId() && $model->audit_status == AuditStatusEnum::SAVE;
    }


    /**
     * 根据货号查询出库成本
     * @param unknown $goods_id
     * @return number
     */
    public function getChukuPrice($goods_id){
        $model = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
        return $model->getChukuPrice();
    }
    /**
     * 计算出库成本价
     * @param WarehouseGoods $model
     */
    /* public function calcChukuPrice($model) {
        //产品线是Au990 ，Au999，Au9999
        if(in_array($model->product_type_id,[9,28,34])){
            $gold_price = \Yii::$app->goldTool->getGoldPrice('XAU');
            $chuku_price = $model->gold_weight * $gold_price * (1 + 0.03);
        }else{
            $chuku_price = $model->cost_price * (1 + 0.05);
        }
        return round ($chuku_price,2);
    } */
    /**
     * 同步数据到库存
     * @param array $goods
     * @param array $applyGoodsList
     * @throws \Exception
     * @return \addons\Purchase\common\models\PurchaseApply $apply
     */
    public function createWarehouseGoods($goods)
    {
        try{
            $warehouseGoods = new WarehouseGoods();
            $warehouseGoods->attributes = $goods;
            $warehouseGoods->created_at = time();
            $warehouseGoods->updated_at = time();
            $warehouseGoods->creator_id = \Yii::$app->user->id;
            if(false === $warehouseGoods->save()) {
                throw new \Exception($this->getError($warehouseGoods));
            }
            return $warehouseGoods;
        }catch (\Exception $e){
            throw $e;
        }

    }









}