<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseDefectiveGoods;
/**
 * 不良返厂单明细 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseDefectiveGoodsForm extends PurchaseDefectiveGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['defective_id', 'receipt_goods_id'], 'required'],
            [['id', 'defective_id', 'receipt_goods_id', 'style_cate_id', 'product_type_id', 'oqc_reason'], 'integer'],
            [['cost_price'], 'number'],
            [['style_sn'], 'string', 'max' => 50],
            [['factory_mo', 'produce_sn'], 'string', 'max' => 30],
            [['goods_remark'], 'string', 'max' => 255],
        ];
    }   
    
}
