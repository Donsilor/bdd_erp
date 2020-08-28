<?php

namespace addons\Warehouse\common\models;

use addons\Sales\common\models\SaleChannel;
use Yii;
use addons\Supply\common\models\Supplier;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_templet".
 *
 * @property int $id ID
 * @property string $batch_sn 批次号
 * @property int $layout_type 版式类型
 * @property string $style_sn 款号
 * @property string $qiban_sn 起版号
 * @property string $goods_name 样板名称
 * @property string $goods_image 图片
 * @property int $channel_id 渠道
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property int $goods_num 数量
 * @property string $suttle_weight 净重(g)
 * @property string $goods_size 成品尺寸
 * @property string $stone_weight 总石重(ct)
 * @property string $stone_size 石头规格
 * @property string $cost_price 成本价
 * @property string $sale_price 销售价
 * @property int $supplier_id 供应商
 * @property int $put_in_type 入库方式
 * @property int $warehouse_id 所在仓库
 * @property string $purchase_sn 采购单编号
 * @property string $receipt_no 收货单号
 * @property int $source_detail_id 商品来源ID
 * @property int $goods_status 库存状态
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseTemplet extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_templet');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_sn', 'style_sn'], 'required'],
            [['layout_type', 'channel_id', 'goods_num', 'supplier_id', 'put_in_type', 'warehouse_id', 'source_detail_id', 'goods_status', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['suttle_weight', 'stone_weight', 'cost_price', 'sale_price'], 'number'],
            [['batch_sn', 'style_sn', 'qiban_sn', 'purchase_sn', 'receipt_no'], 'string', 'max' => 30],
            [['goods_name', 'goods_size', 'stone_size'], 'string', 'max' => 100],
            [['goods_image', 'remark'], 'string', 'max' => 255],
            [['finger', 'finger_hk'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batch_sn' => '批次号',
            'layout_type' => '版式类型',
            'style_sn' => '款号',
            'qiban_sn' => '起版号',
            'goods_name' => '样板名称',
            'goods_image' => '图片',
            'channel_id' => '渠道',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'goods_num' => '数量',
            'suttle_weight' => '净重(g)',
            'goods_size' => '成品尺寸(mm)',
            'stone_weight' => '总石重(ct)',
            'stone_size' => '石头规格',
            'cost_price' => '成本价',
            'sale_price' => '销售价',
            'supplier_id' => '供应商',
            'put_in_type' => '入库方式',
            'warehouse_id' => '所在仓库',
            'purchase_sn' => '采购单编号',
            'receipt_no' => '收货单号',
            'source_detail_id' => '商品来源ID',
            'goods_status' => '库存状态',
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
    /**
     * 销售渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }
}
