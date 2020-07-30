<?php

namespace addons\Warehouse\common\models;

use addons\Supply\common\models\Supplier;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "warehouse_parts".
 *
 * @property int $id
 * @property string $parts_sn 批次号
 * @property string $parts_name 配件名称
 * @property string $parts_type 配件类型
 * @property int $parts_status 配件状态
 * @property string $material_type 材质
 * @property string $style_sn 配件款号
 * @property string $shape 形状
 * @property string $color 颜色
 * @property string $size 尺寸
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property int $parts_num 配件数量
 * @property string $parts_weight 配件重量(g)
 * @property string $cost_price 配件总额
 * @property string $parts_price 配件单价/g
 * @property string $sale_price 销售价
 * @property int $supplier_id 供应商
 * @property int $put_in_type 入库方式
 * @property int $warehouse_id 所在仓库
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseParts extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_parts');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parts_sn', 'parts_name', 'parts_type'], 'required'],
            [['parts_status', 'parts_num', 'supplier_id', 'put_in_type', 'warehouse_id', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['parts_weight', 'cost_price', 'parts_price', 'sale_price'], 'number'],
            [['parts_sn', 'parts_name', 'style_sn'], 'string', 'max' => 30],
            [['parts_type', 'material_type', 'shape', 'color', 'size', 'chain_type', 'cramp_ring'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parts_sn' => '批次号',
            'parts_name' => '配件名称',
            'parts_type' => '配件类型',
            'parts_status' => '配件状态',
            'material_type' => '材质',
            'style_sn' => '配件款号',
            'shape' => '形状',
            'color' => '颜色',
            'size' => '尺寸',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'parts_num' => '配件数量',
            'parts_weight' => '配件重量(g)',
            'cost_price' => '配件总额',
            'parts_price' => '配件单价/g',
            'sale_price' => '销售价',
            'supplier_id' => '供应商',
            'put_in_type' => '入库方式',
            'warehouse_id' => '所在仓库',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * 供应商 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id'])->alias('warehouse');
    }
}
