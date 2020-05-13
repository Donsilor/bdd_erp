<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseReceiptGoodsForm extends PurchaseReceiptGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'receipt_id', 'purchase_sn', 'factory_mo'], 'required'],
            [['receipt_id', 'style_cate_id', 'product_type_id', 'material', 'jintuo_type', 'main_stone', 'main_stone_num', 'main_stone_color', 'main_stone_clarity', 'second_stone1', 'second_stone_num1', 'second_stone2', 'second_stone_num2', 'second_stone3', 'second_stone_num3'], 'integer'],
            [['finger', 'gold_weight', 'gold_price', 'gold_loss', 'gross_weight', 'cost_price', 'main_stone_weight', 'main_stone_price', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2', 'second_stone_price2', 'second_stone_weight3', 'second_stone_price3', 'fee_price', 'parts_price', 'extra_stone_fee', 'tax_fee', 'other_fee'], 'number'],
            [['purchase_sn', 'produce_sn', 'factory_mo', 'cert_id'], 'string', 'max' => 30],
            [['style_sn'], 'string', 'max' => 50],
            [['xiangkou'], 'string', 'max' => 10],
        ];
    }   
    
}
