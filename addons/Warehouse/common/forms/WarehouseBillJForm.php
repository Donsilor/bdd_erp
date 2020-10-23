<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\helpers\StringHelper;

/**
 * 借货单 Form
 *
 */
class WarehouseBillJForm extends WarehouseBill
{
    public $ids;
    public $goods_ids;
    public $goods_list;
    public $lender_id;
    public $lend_status;
    public $restore_num;
    public $est_restore_time;
    public $rel_restore_time;
    public $returned_time;
    public $goods_remark;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['channel_id', 'lender_id', 'est_restore_time'], 'required'],
            [['lend_status'], 'integer'],
            [['rel_restore_time'], 'safe'],
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
            'order_sn' => '参考编号',
            'goods_num' => '借货数量',
            'lender_id' => '借货人',
            'lend_status' => '借货状态',
            'channel_id' => '借货渠道',
            'restore_num' => '还货数量',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
            'est_restore_time' => '预计还货时间',
            'rel_restore_time' => '实际还货时间',
            'returned_time' => '还货日期',
            'audit_time' => '借货时间',
            'goods_remark' => '质检备注',
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
        if(is_array($this->goods_ids)) {
            return $this->goods_ids;
        }
        return StringHelper::explodeIds($this->goods_ids);
    }

    /**
     * 查询商品数据校验
     * {@inheritdoc}
     */
    public function validateGoodsList()
    {
        $goods_ids = $this->getGoodsIds();
        $this->goods_ids = [];
        foreach ($goods_ids as $k => $goods_id) {
            $goods = WarehouseGoods::find()->select(['goods_num','stock_num','do_chuku_num','goods_status'])->where(['goods_id'=>$goods_id])->one();
            if (!$goods) {
                $this->addGoodsError($goods_id, 1,"货号不存在");
            }else if(!in_array($goods->goods_status,[GoodsStatusEnum::HAS_SOLD,GoodsStatusEnum::IN_STOCK])) {
                $this->addGoodsError($goods_id, 1,"商品不是库存或已销售状态");
            }else if(($goods->goods_num - $goods->stock_num-$goods->do_chuku_num) <=0) {
                $this->addGoodsError($goods_id, 1,"商品不符合借货条件");
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
