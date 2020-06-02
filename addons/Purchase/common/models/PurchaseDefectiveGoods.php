<?php

namespace addons\Purchase\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "purchase_defective_goods".
 *
 * @property int $id ID
 * @property int $defective_id 返厂单ID
 * @property int xuhao 序号
 * @property string $style_sn 款式编号
 * @property string $factory_no 工厂模号
 * @property string $produce_sn 布产号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $cost_price 金额
 * @property int $iqc_reason OQC质检未过原因
 * @property string $iqc_remark 备注：IQC未过原因
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
            [['defective_id', 'xuhao'], 'required'],
            [['id', 'defective_id', 'style_cate_id', 'product_type_id', 'iqc_reason', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cost_price'], 'number'],
            [['style_sn'], 'string', 'max' => 50],
            [['factory_mo', 'produce_sn'], 'string', 'max' => 30],
            [['iqc_remark'], 'string', 'max' => 255],
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
            'xuhao' => '采购收货单货品序号',
            'style_sn' => '款式编号',
            'factory_mo' => '工厂模号',
            'produce_sn' => '布产单编号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'cost_price' => '金额',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '货品备注',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias('type');
    }
    /**
     * 款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    }
    /**
     * 关联质检未过原因
     * @return \yii\db\ActiveQuery
     */
    public function getFqc()
    {
        return $this->hasOne(PurchaseFqcConfig::class, ['id'=>'iqc_reason'])->alias('fqc');
    }
}
