<?php

namespace addons\Warehouse\common\models;

use addons\Supply\common\models\ProduceGold;
use Yii;

/**
 * This is the model class for table "warehouse_parts_bill_goods".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property string $parts_name 配件名称
 * @property string $parts_sn 批次号
 * @property string $style_sn 配件款号
 * @property string $parts_type 配件类型
 * @property int $parts_num 配件数量
 * @property double $parts_weight 配件总重(g)
 * @property string $parts_price 配件单价/g
 * @property string $material_type 配件材质
 * @property string $color 颜色
 * @property string $shape 形状
 * @property string $size 尺寸
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $cost_price 配件总额
 * @property string $sale_price 销售价
 * @property int $source_detail_id 来源明细ID
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehousePartsBillGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_parts_bill_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'bill_no', 'bill_type', 'parts_name'], 'required'],
            [['bill_id', 'parts_num', 'source_detail_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['parts_weight', 'parts_price', 'cost_price', 'sale_price'], 'number'],
            [['bill_no', 'parts_name', 'parts_sn', 'style_sn'], 'string', 'max' => 30],
            [['bill_type', 'parts_type', 'material_type', 'color', 'shape', 'size', 'chain_type', 'cramp_ring'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
            [['supplier_id','creator_id','auditor_id'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_id' => '单据ID',
            'bill_no' => '单据编号',
            'bill_type' => '单据类型',
            'parts_name' => '配件名称',
            'parts_sn' => '批次号',
            'style_sn' => '配件款号',
            'parts_type' => '配件类型',
            'parts_num' => '配件数量',
            'parts_weight' => '配件总重(g)',
            'parts_price' => '配件单价/g',
            'material_type' => '配件材质',
            'color' => '颜色',
            'shape' => '形状',
            'size' => '尺寸',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'cost_price' => '配件总额',
            'sale_price' => '销售价',
            'source_detail_id' => '来源明细ID',
            'remark' => '备注',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 盘点单明细附属表
     * @return \yii\db\ActiveQuery
     */
    public function getGoodsW()
    {
        return $this->hasOne(WarehousePartsBillGoodsW::class, ['id'=>'id'])->alias('goodsW');
    }
    /**
     * 单据
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(WarehousePartsBill::class, ['id'=>'bill_id'])->alias('bill');
    }
    /**
     * 配石记录
     * @return \yii\db\ActiveQuery
     */
    public function getProduceParts()
    {
        return $this->hasOne(ProduceParts::class, ['id'=>'source_detail_id'])->alias('produceParts');
    }
}
