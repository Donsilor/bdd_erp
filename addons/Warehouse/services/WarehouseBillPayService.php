<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use addons\Warehouse\common\forms\WarehouseBillPayForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\IsSettleAccountsEnum;
use addons\Warehouse\common\enums\PayContentEnum;

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
        if (!$ids) {
            throw new \Exception("ID不能为空");
        }
        $goods = WarehouseBillTGoodsForm::findAll(['id' => $ids]);
        $pay_amount = $pay_gold_weight = 0;
        foreach ($goods as $good) {
            $form->bill_id = $good->bill_id;
            $pay_amount = bcadd($pay_amount, $good->factory_cost, 3);
            $pay_gold_weight = bcadd($pay_gold_weight, $good->pure_gold, 3);
        }
        if ($form->pay_content == PayContentEnum::FACTORY_COST) {
            $form->pay_amount = $pay_amount ?? 0;
        } elseif ($form->pay_content == PayContentEnum::LAILIAO) {
            if (!$form->pay_material) {
                throw new \Exception("结算材质不能为空");
            }
            $form->pay_gold_weight = $pay_gold_weight ?? 0;
        }
        $form->creator_id = \Yii::$app->user->identity->getId();
        $form->created_at = time();
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        //回写结算状态
        $data = ['pay_status' => IsSettleAccountsEnum::YES_SETTLEMENT, 'pay_id' => $form->id];
        $res = WarehouseBillTGoodsForm::updateAll($data, ['id' => $ids]);
        if (false === $res) {
            throw new \Exception("回写结算状态失败");
        }
        return $form;
    }

    /**
     *  供应商结算验证
     * @param WarehouseBillTGoodsForm $form
     * @throws \Exception
     */
    public function billPayValidate($form)
    {
        $ids = $form->getIds();
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $goods = WarehouseBillTGoodsForm::findOne(['id' => $id]);
                if ($goods->pay_status == IsSettleAccountsEnum::YES_SETTLEMENT) {
                    throw new \Exception("条码号【" . $goods->goods_id . "】已结价，不能重复结价");
                }
            }
        }
    }
}