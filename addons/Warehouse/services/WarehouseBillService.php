<?php

namespace addons\Warehouse\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillLog;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\StatusEnum;
use common\helpers\SnHelper;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillService extends Service
{
    /**
     * 仓储单据明细 tab
     * @param integer $bill_id 单据ID
     * @param integer $bill_type
     * @param $returnUrl URL
     * @param bool $tag
     * @return array
     */
    public function menuTabList($bill_id, $bill_type, $returnUrl = null, $tag = null)
    {

        switch ($bill_type) {

            case BillTypeEnum::BILL_TYPE_M:
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-m/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-m-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])]
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_W:
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-w/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-w-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])]
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_L:
                {
                    if ($tag == 3) {
                        $tab = [
                            1 => ['name' => '单据详情', 'url' => Url::to(['bill-l/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                            3 => ['name' => '单据明细(编辑)', 'url' => Url::to(['bill-l-goods/edit-all', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                            4 => ['name' => '结算商信息', 'url' => Url::to(['bill-pay/index', 'bill_id' => $bill_id, 'tab' => 4, 'returnUrl' => $returnUrl])],
                            5 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 5, 'returnUrl' => $returnUrl])],
                        ];
                    } else {
                        $tab = [
                            1 => ['name' => '单据详情', 'url' => Url::to(['bill-l/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                            2 => ['name' => '单据明细', 'url' => Url::to(['bill-l-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                            4 => ['name' => '结算商信息', 'url' => Url::to(['bill-pay/index', 'bill_id' => $bill_id, 'tab' => 4, 'returnUrl' => $returnUrl])],
                            5 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 5, 'returnUrl' => $returnUrl])],
                        ];
                    }
                    break;
                }
            case BillTypeEnum::BILL_TYPE_B :
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-b/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-b-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_T:
                {
                    if ($tag == 3) {
                        $tab = [
                            1 => ['name' => '单据详情', 'url' => Url::to(['bill-t/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                            3 => ['name' => '单据明细(编辑)', 'url' => Url::to(['bill-t-goods/edit-all', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                            4 => ['name' => '结算商信息', 'url' => Url::to(['bill-pay/index', 'bill_id' => $bill_id, 'tab' => 4, 'returnUrl' => $returnUrl])],
                            5 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 5, 'returnUrl' => $returnUrl])],
                        ];
                    } else {
                        $tab = [
                            1 => ['name' => '单据详情', 'url' => Url::to(['bill-t/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                            2 => ['name' => '单据明细', 'url' => Url::to(['bill-t-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                            4 => ['name' => '结算商信息', 'url' => Url::to(['bill-pay/index', 'bill_id' => $bill_id, 'tab' => 4, 'returnUrl' => $returnUrl])],
                            5 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 5, 'returnUrl' => $returnUrl])],
                        ];
                    }
                    break;
                }
            case BillTypeEnum::BILL_TYPE_C :
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-c/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-c-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_A:
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-a/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-a-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])]
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_J :
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-j/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-j-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_S :
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-s/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-s-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_D :
                {
                    $tab = [
                        1 => ['name' => '单据详情', 'url' => Url::to(['bill-d/view', 'id' => $bill_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
                        2 => ['name' => '单据明细', 'url' => Url::to(['bill-d-goods/index', 'bill_id' => $bill_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
                        3 => ['name' => '日志列表', 'url' => Url::to(['bill-log/index', 'bill_id' => $bill_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
                    ];
                    break;
                }

        }
        return $tab;
    }

    /**
     * 仓储单据汇总
     * @param integer $bill_id
     * @return bool
     */
    public function warehouseBillSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoods::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(sale_price) as total_sale', 'sum(market_price) as total_market'])
            ->where(['bill_id' => $bill_id, 'status' => StatusEnum::ENABLED])
            ->asArray()->one();
        if ($sum) {
            $result = WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_sale' => $sum['total_sale'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        return $result;
    }

    /**
     * 创建收货单据
     *
     * @param array $bill
     * @param array $goods
     * @throws
     */
    public function createWarehouseBillL($bill, $goods)
    {
        $warehouseBill = new WarehouseBill();
        $warehouseBill->bill_no = SnHelper::createBillSn($bill['bill_type']);
        $warehouseBill->attributes = $bill;
        if (false === $warehouseBill->save()) {
            throw new \Exception($this->getError($warehouseBill));
        }
        $bill_id = $warehouseBill->attributes['id'];
        $warehouseGoods = new WarehouseGoods();
        $goods_list = [];
        $bill_goods = [];
        foreach ($goods as $item) {
            $item['goods_id'] = SnHelper::createGoodsId();
            $warehouseGoods->setAttributes($item);
            if (!$warehouseGoods->validate()) {
                throw new \Exception($this->getError($warehouseGoods));
            }
            $goods_list[] = $item;
            $bill_goods[] = [
                'bill_id' => $bill_id,
                'bill_no' => $warehouseBill->bill_no,
                'bill_type' => $warehouseBill->bill_type,
                'goods_id' => $warehouseGoods->goods_id,
                'goods_name' => $warehouseGoods->goods_name,
                'style_sn' => $warehouseGoods->style_sn,
                'goods_num' => $warehouseGoods->goods_num,
                'put_in_type' => $warehouseBill->put_in_type,
                'material' => $warehouseGoods->material,
                'gold_weight' => $warehouseGoods->gold_weight,
                'gold_loss' => $warehouseGoods->gold_loss,
                'diamond_carat' => $warehouseGoods->diamond_carat,
                'diamond_color' => '', //$warehouseGoods->diamond_color,
                'diamond_clarity' => $warehouseGoods->diamond_clarity,
                'diamond_cert_id' => $warehouseGoods->diamond_cert_id,
                'cost_price' => 0,//$warehouseGoods->cost_price,
                'sale_price' => 0,//$warehouseGoods->sale_price,
                'market_price' => $warehouseGoods->market_price,
                'markup_rate' => 0, //$warehouseGoods->markup_rate
                'status' => 1,
                'created_at' => time()
            ];
        }

        $goods_val = [];
        $goods_key = array_keys($goods_list[0]);
        foreach ($goods_list as $item) {
            $goods_val[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $goods_key, $goods_val)->execute();
        if (false === $res) {
            throw new \Exception("保存商品信息失败");
        }

        $bill_goods_val = [];
        $bill_goods_key = array_keys($bill_goods[0]);
        foreach ($bill_goods as $item) {
            $bill_goods_val[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $bill_goods_key, $bill_goods_val)->execute();
        if (false === $res) {
            throw new \Exception("保存收货单据明细失败");
        }
    }

    /**
     * 单据日志
     * @param array $log
     * @return
     * @throws \Exception
     */
    public function createWarehouseBillLog($log)
    {

        $model = new WarehouseBillLog();
        $model->attributes = $log;
        if (false === $model->save()) {
            throw new \Exception($this->getError($model));
        }
        return $model;
    }

    //统计单据明细数量
    public function sumGoodsNum($bill_id)
    {
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('goods_num');
    }

    //统计单据明细成本价
    public function sumCostPrice($bill_id)
    {
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('cost_price');
    }

    //统计单据明细销售价
    public function sumSalePrice($bill_id)
    {
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('sale_price');
    }

    //统计单据明细市场价
    public function sumMarketPrice($bill_id)
    {
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('market_price');
    }
}