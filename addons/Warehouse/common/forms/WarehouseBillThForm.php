<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseGoods;

/**
 * 其它退货单 Form
 *
 */
class WarehouseBillThForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                
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
            $goods = WarehouseGoods::find()->where($where)->one();
            if (!$goods) {
                $flag = false;
                $this->addGoodsError($goods_id, 1,"不存在或者不是库存状态");
            }            
            if($flag){
                $valid_goods_ids.= $goods_id.",";
            }
        }
        if($valid_goods_ids){
            $valid_goods_ids = trim($valid_goods_ids, ',');
        }
        return $flag;
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
        return $searchGoods;
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
