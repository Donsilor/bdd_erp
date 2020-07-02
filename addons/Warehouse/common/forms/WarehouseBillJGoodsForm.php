<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\helpers\StringHelper;

/**
 * 借货单 Form
 *
 */
class WarehouseBillJGoodsForm extends WarehouseBillGoods
{
    public $ids;
    public $goods_ids;
    public $qc_status;
    public $restore_time;
    public $qc_remark;
    public $receive_remark;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['qc_status'], 'integer'],
            [['goods_ids', 'receive_remark', 'qc_remark'], 'string', 'max' => 255],
            [['restore_time'], 'safe'],
        ];
        return array_merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'goods_ids' => '货号',
            'qc_status' => '质检状态',
            'qc_remark' => '质检备注',
            'restore_time' => '还货日期',
            'receive_remark' => '接收备注',
            'from_warehouse_id'=>'出库仓库',
            'to_warehouse_id'=>'入库仓库'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
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
     * {@inheritdoc}
     * @throws
     */
    public function getGoodsList()
    {
        $goods_ids = $this->getGoodsIds();
        $bill = WarehouseBillJForm::find()->select(['bill_no'])->where(['id'=>$this->bill_id])->one();
        foreach ($goods_ids as $goods_id) {
            $goods = WarehouseGoods::find()->where(['goods_id' => $goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(!$goods){
                throw new \Exception("货号{$goods_id}不存在或者不是库存中");
            }
            $goods_info = [];
            $goods_info['id'] = null;
            $goods_info['goods_id'] = $goods_id;
            $goods_info['bill_id'] = $this->bill_id;
            $goods_info['bill_no'] = $bill->bill_no;
            $goods_info['bill_type'] = $bill->bill_type;
            $goods_info['style_sn'] = $goods->style_sn;
            $goods_info['goods_name'] = $goods->goods_name;
            $goods_info['goods_num'] = $goods->goods_num;
            $goods_info['put_in_type'] = $goods->put_in_type;
            $goods_info['warehouse_id'] = $goods->warehouse_id;
            $goods_info['from_warehouse_id'] = $goods->warehouse_id;
            $goods_info['material'] = $goods->material;
            $goods_info['gold_weight'] = $goods->gold_weight;
            $goods_info['gold_loss'] = $goods->gold_loss;
            $goods_info['diamond_carat'] = $goods->diamond_carat;
            $goods_info['diamond_color'] = $goods->diamond_color;
            $goods_info['diamond_clarity'] = $goods->diamond_clarity;
            $goods_info['diamond_cert_id'] = $goods->diamond_cert_id;
            $goods_info['cost_price'] = $goods->cost_price;
            $goods_info['sale_price'] = $goods->market_price;
            $goods_info['market_price'] = $goods->market_price;
            $goods_list[] = $goods_info;
        }
        return $goods_list??[];
    }
}
