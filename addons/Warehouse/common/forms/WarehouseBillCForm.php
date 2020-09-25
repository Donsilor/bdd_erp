<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\enums\DeliveryTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 其它出库单 Form
 *
 */
class WarehouseBillCForm extends WarehouseBill
{
    public $ids;
    public $goods_ids;
    public $file;

    private $_goodsErrors;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['delivery_type', 'channel_id'], 'required'],
            [['goods_ids'], 'string']
        ];
        return array_merge(parent::rules(), $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels(), [
            'order_sn' => '参考编号/订单号',
            'channel_id' => '出库渠道',
            'total_cost' => '出库总成本',
            'salesman_id' => '销售人/接收人',
            'goods_ids' => '货号',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        if ($this->ids) {
            return StringHelper::explode($this->ids);
        }
        return [];
    }

    /**
     * 批量获取货号
     */
    public function getGoodsIds()
    {
        return StringHelper::explodeIds($this->goods_ids);
    }

    /**
     * 查询商品数据校验
     * {@inheritdoc}
     */
    public function loadGoods()
    {
        $valid_goods_ids = "";
        $goodsIds = $this->getGoodsIds();
        foreach ($goodsIds as $k => $goods_id) {
            $flag = true;
            $where = [
                'goods_id' => $goods_id,
                'goods_status' => GoodsStatusEnum::IN_STOCK
            ];
            $goods = WarehouseGoods::find()->where($where)->one();
            if (!$goods) {
                $flag = false;
                $this->addGoodsError($goods_id, 1,"不存在或者不是库存状态");
            }
            $data = [
                DeliveryTypeEnum::PROXY_PRODUCE,
                DeliveryTypeEnum::PART_GOODS,
                DeliveryTypeEnum::ASSEMBLY,
            ];
            if (in_array($this->delivery_type, $data)) {
                if ($goods->supplier_id != $this->supplier_id) {
                    $flag = false;
                    $this->addGoodsError($goods_id, 2,"供应商与单据的供应商不一致");
                }
                /*if($goods->put_in_type != $bill->put_in_type){
                    return $this->message("货号{$goods_id}的入库方式与单据的入库方式不一致", $this->redirect(Yii::$app->request->referrer), 'error');
                }*/
            }
            if($flag){
                $valid_goods_ids.= $goods_id.",";
            }
        }
        return $valid_goods_ids ?? "";
    }

    /**
     * @param WarehouseBillCForm $form
     * {@inheritdoc}
     */
    public function getSearchGoods()
    {
        $searchGoods = [];
        $goodsIds = $this->getGoodsIds();
        if($goodsIds){
            $goodsList = WarehouseGoods::find()->where(['goods_id'=>$goodsIds])->all();
            foreach ($goodsList as $goods) {
                $searchGoods[] = [
                    'id' => null,
                    'goods_id' => $goods->goods_id,
                    'bill_id' => $this->id,
                    'bill_no' => $this->bill_no,
                    'bill_type' => $this->bill_type,
                    'style_sn' => $goods->style_sn,
                    'goods_name' => $goods->goods_name,
                    'goods_num' => $goods->goods_num,
                    'put_in_type' => $goods->put_in_type,
                    'warehouse_id' => $goods->warehouse_id,
                    'from_warehouse_id' => $goods->warehouse_id,
                    'material' => $goods->material,
                    'gold_weight' => $goods->gold_weight,
                    'gold_loss' => $goods->gold_loss,
                    'diamond_carat' => $goods->diamond_carat,
                    'diamond_color' => $goods->diamond_color,
                    'diamond_clarity' => $goods->diamond_clarity,
                    'diamond_cert_id' => $goods->diamond_cert_id,
                    'cost_price' => $goods->cost_price,
                    'sale_price' => $goods->market_price,
                    'market_price' => $goods->market_price,
                ];
            }
        }
        return $searchGoods ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function addGoodsError($goods_id, $type, $error)
    {
        $this->_goodsErrors[$goods_id][$type] = $error;
    }

    /**
     * @return string
     */
    public function getGoodsMessage()
    {
        $message = '';
        if ($this->_goodsErrors) {
            //发生错误
            foreach ($this->_goodsErrors as $g => $errors) {
                $message .= "货号:【" . $g. "】";
                foreach ($errors as $error) {
                    $message .= $error . ";";
                }
                $message .= "<br>";
            }
        }
        return $message;
    }
}
