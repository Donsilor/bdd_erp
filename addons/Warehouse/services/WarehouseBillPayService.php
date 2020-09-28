<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use addons\Warehouse\common\forms\WarehouseBillPayForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\IsSettleAccountsEnum;

/**
 * 供应商结算
 * Class WarehouseBillLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillPayService extends Service
{
    /**
     * 创建结算信息
     * @param WarehouseBillPayForm $form
     * @throws \Exception
     * @return object
     */
    public function createBillPay($form)
    {
        $ids = $form->getIds();
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
        return $form;
    }

    /**
     *  供应商结算验证
     * @param WarehouseBillTGoodsForm $form
     * @throws \Exception
     */
    public function billPayValidate($form){
        $ids = $form->getIds();
        if(is_array($ids)){
            foreach ($ids as $id) {
                $goods = WarehouseBillTGoodsForm::findOne(['id'=>$id]);
                if($goods->pay_status == IsSettleAccountsEnum::YES_SETTLEMENT){
                    throw new \Exception("条码号【".$goods->goods_id."】已结价，不能重复结价");
                }
            }
        }
    }
}