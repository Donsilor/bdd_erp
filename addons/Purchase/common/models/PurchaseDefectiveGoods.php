<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_defective_goods".
 *
 * @property int $id ID
 * @property int $defective_id 返厂单ID
 * @property int $receipt_goods_id 关联purchase_receipt_goods表ID
 * @property string $style_sn 款式编号
 * @property string $factory_no 工厂模号
 * @property string $produce_sn 布产号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $cost_price 金额
 * @property int $oqc_reason OQC质检未过原因
 * @property string $goods_remark 备注：IQC未过原因
 */
class PurchaseDefectiveGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_defective_goods');
    }

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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'defective_id' => '返厂单ID',
            'receipt_goods_id' => '采购收货单商品序号',
            'style_sn' => '款式编号',
            'factory_no' => '工厂模号',
            'produce_sn' => '布产单编号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'cost_price' => '金额',
            'oqc_reason' => 'OQC质检未过原因',
            'goods_remark' => '备注：IQC未过原因',
        ];
    }
}
