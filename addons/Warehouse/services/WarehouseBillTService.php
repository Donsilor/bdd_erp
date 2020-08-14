<?php

namespace addons\Warehouse\services;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use addons\Warehouse\common\forms\WarehouseBillTForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use common\enums\StatusEnum;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\models\Style;
use addons\Warehouse\common\models\WarehouseBillGoodsL;

/**
 * 其他收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillTService extends Service
{

    /**
     * 单据汇总
     * @param int $bill_id
     * @return bool
     * @throws
     */
    public function warehouseBillTSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoodsL::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(market_price) as total_market'])
            ->where(['bill_id' => $bill_id])
            ->asArray()->one();
        if ($sum) {
            $result = WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        return $result;
    }

    /**
     * 添加明细
     * @param WarehouseBillTGoodsForm $form
     * @throws
     */
    public function addBillTGoods($form)
    {

        if (!$form->goods_sn) {
            throw new \Exception("款号/起版号不能为空");
        }
        if (!$form->goods_num) {
            throw new \Exception("商品数量必填");
        }
        if (!is_numeric($form->goods_num)) {
            throw new \Exception("商品数量不合法");
        }
        if ($form->goods_num <= 0) {
            throw new \Exception("商品数量必须大于0");
        }
        if ($form->goods_num > 100) {
            throw new \Exception("一次最多只能添加100个商品，可分多次添加");
        }
        $goods_num = 1;
        if ($form->is_wholesale) {//批发
            $goods_num = $form->goods_num;
            $form->goods_num = 1;
        }
        $style = Style::find()->where(['style_sn' => $form->goods_sn])->one();
        if (!$style) {
            $qiban = Qiban::find()->where(['qiban_sn' => $form->goods_sn])->one();
            if (!$qiban) {
                throw new \Exception("[款号/起版号]不存在");
            } elseif ($qiban->status != StatusEnum::ENABLED) {
                throw new \Exception("起版号不可用");
            } else {
                $exist = WarehouseBillGoodsL::find()->where(['bill_id' => $form->bill_id, 'qiban_sn' => $form->goods_sn, 'status' => StatusEnum::ENABLED])->count();
                if ($exist) {
                    //throw new \Exception("起版号已添加过");
                }
                if ($form->cost_price) {
                    $qiban->cost_price = $form->cost_price;
                }
                //$qiban = new Qiban();
                $goods = [
                    'goods_sn' => $form->goods_sn,
                    'goods_name' => $qiban->qiban_name,
                    'style_id' => $qiban->id,
                    'style_sn' => $form->goods_sn,
                    'goods_image' => $style->style_image,
                    'qiban_type' => $qiban->qiban_type,
                    'product_type_id' => $qiban->product_type_id,
                    'style_cate_id' => $qiban->style_cate_id,
                    'style_channel_id' => $qiban->style_channel_id,
                    'style_sex' => $qiban->style_sex,
                    'goods_num' => $goods_num,
                    'jintuo_type' => $qiban->jintuo_type,
                    'cost_price' => bcmul($qiban->cost_price, $goods_num, 3),
                    //'market_price' => $style->market_price,
                    'is_inlay' => $qiban->is_inlay,
                    'remark' => $qiban->remark,
                    'creator_id' => \Yii::$app->user->identity->getId(),
                    'created_at' => time(),
                ];
            }
        } elseif ($style->status != StatusEnum::ENABLED) {
            throw new \Exception("款号不可用");
        } else {
            if ($form->cost_price) {
                $style->cost_price = $form->cost_price;
            }
            //$style = new Style();
            $goods = [
                'goods_sn' => $form->goods_sn,
                'goods_name' => $style->style_name,
                'style_id' => $style->id,
                'style_sn' => $form->goods_sn,
                'goods_image' => $style->style_image,
                'qiban_type' => QibanTypeEnum::NON_VERSION,
                'product_type_id' => $style->product_type_id,
                'style_cate_id' => $style->style_cate_id,
                'style_channel_id' => $style->style_channel_id,
                'style_sex' => $style->style_sex,
                'goods_num' => $goods_num,
                'jintuo_type' => JintuoTypeEnum::Chengpin,
                'cost_price' => bcmul($style->cost_price, $goods_num, 3),
                'is_inlay' => $style->is_inlay,
                //'market_price' => $style->market_price,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
        }
        $bill = WarehouseBill::findOne(['id' => $form->bill_id]);
        $goodsM = new WarehouseBillGoodsL();
        $goodsInfo = [];
        for ($i = 0; $i < $form->goods_num; $i++) {
            $goodsInfo[$i] = $goods;
            $goodsInfo[$i]['bill_id'] = $form->bill_id;
            $goodsInfo[$i]['bill_no'] = $bill->bill_no;
            $goodsInfo[$i]['bill_type'] = $bill->bill_type;
            $goodsInfo[$i]['goods_id'] = SnHelper::createGoodsId();
            $goodsInfo[$i]['is_wholesale'] = $form->is_wholesale;//批发
            $goodsInfo[$i]['auto_goods_id'] = $form->auto_goods_id;
            $goodsM->setAttributes($goodsInfo[$i]);
            if (!$goodsM->validate()) {
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goodsInfo[0]);
        foreach ($goodsInfo as $item) {
            $value[] = array_values($item);
            if (count($value) >= 10) {
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建收货单据明细失败1");
                }
                $value = [];
            }
        }
        if (!empty($value)) {
            $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
            if (false === $res) {
                throw new \Exception("创建收货单据明细失败2");
            }
        }

        $this->warehouseBillTSummary($form->bill_id);
    }

    /**
     *
     * 同步更新全部商品价格
     * @param WarehouseBillTForm $form
     * @return object
     * @throws
     */
    public function syncUpdatePriceAll($form)
    {
        $goods = WarehouseBillTGoodsForm::findAll(['bill_id'=>$form->id]);
        if($goods){
            foreach ($goods as $good) {
                $this->syncUpdatePrice($good);
            }
        }
        return $goods;
    }

    /**
     *
     * 含耗重=(净重*(1+损耗))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateLossWeight($form)
    {
        return bcmul($form->suttle_weight, $form->gold_loss, 3) ?? 0;
    }

    /**
     *
     * 金料额=(金价*净重*(1+损耗))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateGoldAmount($form)
    {
        return bcmul($form->gold_price, $this->calculateLossWeight($form), 3) ?? 0;
    }

    /**
     *
     * 主石成本=(主石重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMainStoneCost($form)
    {
        return bcmul($form->main_stone_weight, $form->main_stone_price, 3) ?? 0;
    }

    /**
     *
     * 副石1成本=(副石1重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStone1Cost($form)
    {
        return bcmul($form->second_stone_weight1, $form->second_stone_price1, 3) ?? 0;
    }

    /**
     *
     * 副石2成本=(副石2重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStone2Cost($form)
    {
        return bcmul($form->second_stone_weight2, $form->second_stone_price2, 3) ?? 0;
    }

    /**
     *
     * 副石3成本=(副石3重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStone3Cost($form)
    {
        return bcmul($form->second_stone_weight3, $form->second_stone_price3, 3) ?? 0;
    }

    /**
     *
     * 配件额=(配件重*配件金价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePartsAmount($form)
    {
        return bcmul($form->parts_gold_weight, $form->parts_price, 3) ?? 0;
    }

    /**
     *
     * 配石费=((副石重/数量)小于0.03ct的，*数量*配石工费)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePeishiFee($form)
    {
        return bcmul($form->parts_gold_weight, $form->parts_price, 3) ?? 0;
    }

    /**
     *
     * 基本工费=(克工费*含耗重)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateBasicGongFee($form)
    {
        return bcmul($form->gong_fee, $this->calculateLossWeight($form), 3) ?? 0;
    }

    /**
     *
     * 总副石数量=(副石1数量+副石2数量+副石3数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStoneNum($form)
    {
        return bcadd(bcadd($form->second_stone_num1, $form->second_stone_num2, 3), $form->second_stone_num3, 3) ?? 0;
    }

    /**
     *
     * 镶石费=(镶石单价*总副石数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateXiangshiFee($form)
    {
        return bcmul($form->xianqian_price, $this->calculateSecondStoneNum($form), 3) ?? 0;
    }

    /**
     *
     * 工厂成本=(基本工费+镶石费+补口费+超石费+配石费+配石工费+配件工费+税费+版费
     *  +分色/分件费+表面工艺费+喷拉砂费+证书费+其他费用)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateFactoryCost($form)
    {
        return bcmul($form->xianqian_price, $this->calculateSecondStoneNum($form), 3) ?? 0;
    }

    /**
     *
     * 公司总成本(成本价)=(金料额+主石金额+副石1金额+副石2金额+配件额+工厂成本)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateCostPrice($form)
    {
        return bcmul($form->xianqian_price, $this->calculateSecondStoneNum($form), 3) ?? 0;
    }

    /**
     *
     * 标签价(市场价)=(公司总成本*倍率)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMarketPrice($form)
    {
        return bcmul($form->markup_rate, $this->calculateCostPrice($form), 3) ?? 0;
    }

    /**
     *
     * 同步更新商品价格
     * @param WarehouseBillTGoodsForm $form
     * @return object
     * @throws
     */
    public function syncUpdatePrice($form)
    {
        if (!$form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $form->lncl_loss_weight = $this->calculateLossWeight($form);
        $form->gold_amount = $this->calculateGoldAmount($form);
        $form->main_stone_amount = $this->calculateMainStoneCost($form);
        $form->second_stone_amount1 = $this->calculateSecondStone1Cost($form);
        $form->second_stone_amount2 = $this->calculateSecondStone2Cost($form);
        $form->second_stone_amount3 = $this->calculateSecondStone3Cost($form);
        $form->parts_amount = $this->calculatePartsAmount($form);
        $form->peishi_fee = $this->calculatePeishiFee($form);
        $form->basic_gong_fee = $this->calculateBasicGongFee($form);
        $form->xianqian_fee = $this->calculateXiangshiFee($form);
        $form->factory_cost = $this->calculateFactoryCost($form);
        $form->cost_price = $this->calculateCostPrice($form);
        $form->market_price = $this->calculateMarketPrice($form);
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        return $form;
    }

}