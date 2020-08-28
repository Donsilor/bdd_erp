<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use Yii;
use addons\Supply\common\models\Supplier;

/**
 * This is the model class for table "warehouse_gold".
 *
 * @property int $id
 * @property string $gold_sn 批次号
 * @property string $gold_name 金料名称
 * @property string $gold_type 金料类型
 * @property string $style_sn 金料款号
 * @property int $gold_status 库存状态
 * @property int $supplier_id 供应商
 * @property int $gold_num 金料数量
 * @property string $gold_weight 库存重量(g)
 * @property string $first_weight 原重量(g)
 * @property string $cost_price 金料总额
 * @property string $incl_tax_price 含税总额
 * @property string $gold_price 金料单价/g
 * @property string $sale_price 销售价
 * @property int $put_in_type 入库方式
 * @property int $warehouse_id 所在仓库
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseGold extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_gold');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gold_sn', 'gold_name', 'gold_type'], 'required'],
            [['supplier_id', 'gold_num', 'warehouse_id', 'put_in_type', 'gold_status', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'first_weight', 'cost_price', 'gold_price', 'sale_price', 'incl_tax_price'], 'number'],
            [['gold_sn', 'gold_name', 'style_sn'], 'string', 'max' => 30],
            [['gold_type'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
            [['gold_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gold_sn' => '批次号',
            'gold_name' => '金料名称',
            'gold_type' => '金料类型',
            'gold_status' => '库存状态',
            'style_sn' => '金料款号',
            'gold_num' => '金料数量',
            'gold_weight' => '库存重量(g)',
            'first_weight' => '入库金重(g)',
            'cost_price' => '金料总额',
            'incl_tax_price' => '含税总额',
            'gold_price' => '金料单价/g',
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
