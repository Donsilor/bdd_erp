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
    public $goods_list;
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
            [['goods_ids'], 'string'],
            [['receive_remark', 'qc_remark'], 'string', 'max' => 255],
            [['restore_time', 'goods_list'], 'safe'],
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
            'goods_ids' => '条码号',
            'goods_num' => '借货数量',
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
            }else if(($goods->goods_num - $goods->stock_num-$goods->do_chuku_num) <0) {
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
