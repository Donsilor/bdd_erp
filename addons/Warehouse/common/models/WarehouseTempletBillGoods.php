<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_templet_bill_goods".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property string $goods_name 样板名称
 * @property string $goods_image 图片
 * @property string $batch_sn 批次号
 * @property string $style_sn 款号
 * @property string $qiban_sn 起版号
 * @property int $layout_type 版式类型
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $suttle_weight 净重(g)
 * @property int $goods_num 数量
 * @property string $goods_weight 总重
 * @property string $goods_size 尺寸
 * @property string $stone_weight 总石重(ct)
 * @property string $stone_size 石头规格
 * @property string $cost_price 成本价
 * @property string $sale_price 销售价
 * @property int $source_detail_id 来源明细ID
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseTempletBillGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_templet_bill_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'bill_no', 'bill_type', 'goods_name'], 'required'],
            [['bill_id', 'layout_type', 'goods_num', 'source_detail_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['suttle_weight', 'goods_weight', 'stone_weight', 'cost_price', 'sale_price'], 'number'],
            [['bill_no', 'goods_name', 'batch_sn', 'style_sn', 'qiban_sn'], 'string', 'max' => 30],
            [['bill_type', 'finger', 'finger_hk'], 'string', 'max' => 10],
            [['goods_image', 'remark'], 'string', 'max' => 255],
            [['goods_size', 'stone_size'], 'string', 'max' => 100],
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
            'goods_name' => '样板名称',
            'goods_image' => '图片',
            'batch_sn' => '批次号',
            'style_sn' => '款号',
            'qiban_sn' => '起版号',
            'layout_type' => '版式类型',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'suttle_weight' => '净重(g)',
            'goods_num' => '数量',
            'goods_weight' => '总重(g)',
            'goods_size' => '尺寸(mm)',
            'stone_weight' => '总石重(ct)',
            'stone_size' => '石头规格',
            'cost_price' => '成本价',
            'sale_price' => '销售价',
            'source_detail_id' => '来源明细ID',
            'remark' => '备注',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
