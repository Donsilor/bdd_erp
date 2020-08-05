<?php

namespace addons\Warehouse\common\models;

use addons\Sales\common\models\SaleChannel;
use Yii;
use addons\Supply\common\models\ProduceStone;

/**
 * This is the model class for table "warehouse_stone_bill_goods".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property string $stone_name 石料名称
 * @property string $stone_sn 石料编号
 * @property string $stone_type 商品类型
 * @property string $style_sn 石料款号
 * @property string $cert_type 证书类型
 * @property string $cert_id 证书号
 * @property string $carat 石重
 * @property string $shape 形状
 * @property string $color 颜色
 * @property string $clarity 净度
 * @property string $cut 切工
 * @property string $polish 抛光
 * @property string $fluorescence 荧光
 * @property string $symmetry 对称
 * @property int $stone_num 石包总粒数
 * @property string $stone_weight 石包总重量
 * @property string $stone_colour 石料色彩
 * @property string $stone_norms 规格
 * @property string $stone_size 石料尺寸(mm)
 * @property string $cost_price 成本价
 * @property string $stone_price 石料单价/ct
 * @property string $sale_price 销售价格
 * @property int $channel_id 渠道
 * @property int $source_detail_id 来源明细ID
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseStoneBillGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_stone_bill_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'bill_no', 'bill_type', 'stone_name'], 'required'],
            [['bill_id', 'stone_num', 'channel_id', 'source_detail_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['carat', 'stone_weight', 'cost_price', 'stone_price', 'sale_price'], 'number'],
            [['bill_no', 'stone_name', 'stone_sn', 'style_sn', 'stone_size'], 'string', 'max' => 30],
            [['bill_type', 'stone_type', 'cert_type', 'stone_colour'], 'string', 'max' => 10],
            [['cert_id', 'shape', 'color', 'clarity', 'cut', 'polish', 'fluorescence', 'symmetry'], 'string', 'max' => 20],
            [['stone_norms'], 'string', 'max' => 100],
            [['remark'], 'string', 'max' => 255],
            [['supplier_id','creator_id','auditor_id'], 'safe'],
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
            'stone_name' => '石料名称',
            'stone_sn' => '石料编号',
            'stone_type' => '商品类型',
            'style_sn' => '石料款号',
            'cert_type' => '证书类型',
            'cert_id' => '证书号',
            'carat' => '石重',
            'shape' => '形状',
            'color' => '颜色',
            'clarity' => '净度',
            'cut' => '切工',
            'polish' => '抛光',
            'fluorescence' => '荧光',
            'symmetry' => '对称',
            'stone_num' => '石包总粒数',
            'stone_weight' => '石包总重量',
            'stone_colour' => '石料色彩',
            'stone_norms' => '规格',
            'stone_size' => '石料尺寸(mm)',
            'cost_price' => '成本价',
            'stone_price' => '石料单价/ct',
            'sale_price' => '销售价格',
            'channel_id' => '渠道',
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
        return $this->hasOne(WarehouseStoneBillGoodsW::class, ['id'=>'id'])->alias('goodsW');
    }
    /**
     * 配石记录
     * @return \yii\db\ActiveQuery
     */
    public function getProduceStone()
    {
        return $this->hasOne(ProduceStone::class, ['id'=>'source_detail_id'])->alias('produceStone');
    }
	/**
     * 单据
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(WarehouseStoneBill::class, ['id'=>'bill_id'])->alias('bill');
    }
    /**
     * 对应渠道模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id']);
    }
}
