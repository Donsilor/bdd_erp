<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_produce_parts".
 *
 * @property int $id id主键
 * @property int $produce_id 布产ID
 * @property string $produce_sn 布产编号
 * @property int $supplier_id 供应商
 * @property string $delivery_no 送件单号
 * @property string $from_order_sn 来源单号
 * @property int $from_type 来源类型
 * @property string $style_sn 配件款号
 * @property string $parts_name 配件名称
 * @property string $parts_type 配件类型
 * @property int $parts_num 配件数量
 * @property string $parts_weight 配件重量
 * @property string $material_type 配件材质
 * @property string $parts_shape 配件形状
 * @property string $parts_color 配件颜色
 * @property string $parts_size 配件尺寸
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $parts_spec 配件规格
 * @property int $caigou_time 采购时间（记录最新的一次采购时间）
 * @property int $songjian_time 已送生产部时间(已送生产部的最新一次时间)
 * @property int $peijian_time 配件中时间（操作配件中的最新时间）
 * @property string $caigou_user 采购人（操作采购中的人员）
 * @property string $songjian_user 送件人（已送生产部操作人员）
 * @property string $remark 备注
 * @property string $peijian_user 配件人（配件中操作人员）
 * @property int $peijian_status 配件状态（需建数据字典）
 * @property string $peijian_remark 配件备注
 * @property int $creator_id 创建人ID
 * @property string $creator_name 创建人
 * @property int $created_at 添加时间
 * @property int $updated_at
 */
class ProduceParts extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_parts');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produce_id', 'supplier_id', 'from_type', 'parts_num', 'caigou_time', 'songjian_time', 'peijian_time', 'peijian_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['parts_weight'], 'number'],
            [['produce_sn', 'delivery_no', 'from_order_sn', 'caigou_user', 'songjian_user', 'style_sn', 'peijian_user', 'creator_name'], 'string', 'max' => 30],
            [['parts_type', 'material_type', 'parts_shape', 'parts_color', 'parts_size', 'chain_type', 'cramp_ring'], 'string', 'max' => 10],
            [['parts_spec', 'remark', 'peijian_remark'], 'string', 'max' => 255],
            [['parts_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produce_id' => '布产ID',
            'produce_sn' => '布产编号',
            'supplier_id' => '供应商',
            'delivery_no' => '送件单号',
            'from_order_sn' => '来源单号',
            'from_type' => '来源类型',
            'style_sn' => '配件款号',
            'parts_name' => '配件名称',
            'parts_type' => '配件类型',
            'parts_num' => '配件数量',
            'parts_weight' => '配件重量',
            'material_type' => '配件材质',
            'parts_shape' => '配件形状',
            'parts_color' => '配件颜色',
            'parts_size' => '配件尺寸',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'parts_spec' => '配件规格',
            'caigou_time' => '采购时间',
            'songjian_time' => '已送生产部时间',
            'peijian_time' => '配件最新时间',
            'caigou_user' => '采购人',
            'songjian_user' => '送件人',
            'remark' => '备注',
            'peijian_user' => '配件人',
            'peijian_status' => '配件状态',
            'peijian_remark' => '配件备注',
            'creator_id' => '创建人ID',
            'creator_name' => '创建人',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * 配件明细   一对多
     * @return \yii\db\ActiveQuery
     */
    public function getPartsGoods()
    {
        return $this->hasMany(ProducePartsGoods::class, ['id'=>'id'])->alias('partsGoods');
    }
    /**
     * 布产单   一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProduce()
    {
        return $this->hasOne(Produce::class, ['id'=>'produce_id'])->alias('produce');
    }
    /**
     * 对应供应商模型
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
}
