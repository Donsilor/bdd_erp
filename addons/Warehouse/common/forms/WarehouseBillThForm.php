<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\StringHelper;
use addons\Warehouse\common\enums\GoodsStatusEnum;

/**
 * 其它退货单 Form
 *
 */
class WarehouseBillThForm extends WarehouseBill
{
    
    //货号集合
    public $goods_ids;
    //商品列表
    public $goods_list;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
             [['item_type', 'channel_id'], 'required'],
             [['goods_ids','goods_list'],'safe']
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
              'goods_num' =>'退货数量',
              'item_type' =>'退货原因',
              'channel_id'=>'退货渠道', 
              'salesman_id'=>'退货人',
              'remark'=>'退货备注',
              'order_sn'=>'参考编号/订单号',
              'total_cost'=>'退货成本总额',
              'goods_ids' => '货号'
        ]);
    }    
    /**
     * 批量获取货号
     */
    public function getGoodsIds()
    {
        if(is_array($this->goods_ids)) {
            return $this->goods_ids;
        }
        return StringHelper::explodeIds($this->goods_ids);
    }
    
    /**
     * 查询商品数据校验
     * {@inheritdoc}
     */
    public function checkGoodsIds()
    {   
        $goods_ids = $this->getGoodsIds();
        $this->goods_ids = [];
        foreach ($goods_ids as $k => $goods_id) {
            $goods = WarehouseGoods::find()->select(['goods_num','stock_num','goods_status'])->where(['goods_id'=>$goods_id])->one();
            if (!$goods) {
                $this->addGoodsError($goods_id, 1,"货号不存在");
            }else if(!in_array($goods->goods_status,[GoodsStatusEnum::HAS_SOLD,GoodsStatusEnum::IN_STOCK])) {
                $this->addGoodsError($goods_id, 1,"商品不是库存或已销售状态");
            }else if($goods->stock_num >= $goods->goods_num) {
                $this->addGoodsError($goods_id, 1,"商品不符合退货条件");
            }else {
                $this->goods_ids[] = $goods_id;
            }
        }
        return empty($this->_goodsErrors) ? true : false;
    }
    
    /**
     * {@inheritDoc}
     */
    private $_goodsErrors;
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
