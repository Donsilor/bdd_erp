<?php

namespace addons\Warehouse\services;


use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillBForm;
use addons\Warehouse\common\forms\WarehouseBillWForm;
use common\enums\AuditStatusEnum;
use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\Url;
use yii\db\Exception;

/**
 * 退货返厂单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillBService extends WarehouseBillService
{

    /**
     * 退货返厂单审核
     * @param WarehouseBillBForm $form
     */
    public function auditBillB($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            //$form->status = StatusEnum::ENABLED;
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            //$form->status = StatusEnum::DISABLED;
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        $billGoods = WarehouseBillGoods::find()->select('goods_id')->where(['bill_id' => $form->id])->asArray()->all();
        if(empty($billGoods)){
            throw new \Exception("单据明细不能为空");
        }
        $goods_ids = array_column($billGoods, 'goods_id');
        $condition = ['goods_status' => GoodsStatusEnum::IN_RETURN_FACTORY, 'goods_id' => $goods_ids];
        $status = $form->audit_status == AuditStatusEnum::PASS ? GoodsStatusEnum::HAS_RETURN_FACTORY : GoodsStatusEnum::IN_RETURN_FACTORY;
        WarehouseGoods::updateAll(['goods_status' => $status], $condition);
    }

    /**
     * 退货返厂单关闭
     * @param WarehouseBillBForm $form
     */
    public function closeBillB($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        //更新库存状态
        $billGoods = WarehouseBillGoods::find()->where(['bill_id' => $form->id])->select(['goods_id'])->all();
        foreach ($billGoods as $goods){
            $res = WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK],['goods_id' => $goods->goods_id, 'goods_status' => GoodsStatusEnum::IN_RETURN_FACTORY]);
            if(!$res){
                throw new Exception("商品{$goods->goods_id}不是返厂中或者不存在，请查看原因");
            }
        }
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
    }
}