<?php

namespace addons\Warehouse\common\models;

use Yii;
use addons\Supply\common\models\ProduceGold;

/**
 * This is the model class for table "warehouse_gold_bill_goods".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property string $gold_sn 批次号
 * @property string $gold_name 金料名称
 * @property string $style_sn 金料款号
 * @property string $gold_type 商品类型
 * @property int $gold_num 金料总数
 * @property string $gold_weight 金料总重量
 * @property string $cost_price 成本价
 * @property string $incl_tax_price 含税总额
 * @property string $gold_price 单价
 * @property string $sale_price 销售价格
 * @property int $source_detail_id 来源明细ID
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseGoldBillGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_gold_bill_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'bill_type', 'gold_name'], 'required'],
            [['bill_id', 'gold_num', 'source_detail_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'cost_price', 'gold_price', 'sale_price', 'incl_tax_price'], 'number'],
            [['bill_type', 'gold_type'], 'string', 'max' => 10],
            [['bill_no', 'gold_sn', 'gold_name', 'style_sn'], 'string', 'max' => 30],
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
            'gold_sn' => '金料编号',
            'gold_name' => '金料名称',
            'gold_type' => '金料类型',
            'style_sn' => '金料款号',
            'gold_num' => '金料总数',
            'gold_weight' => '金料总重(g)',
            'gold_price' => '金料单价/g',
            'cost_price' => '金料总额',
            'incl_tax_price' => '含税总额',
            'sale_price' => '销售价格',
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
        return $this->hasOne(WarehouseGoldBillGoodsW::class, ['id'=>'id'])->alias('goodsW');
    }
    /**
     * 单据
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(WarehouseGoldBill::class, ['id'=>'bill_id'])->alias('bill');
    }
    /**
     * 配石记录
     * @return \yii\db\ActiveQuery
     */
    public function getProduceGold()
    {
        return $this->hasOne(ProduceGold::class, ['id'=>'source_detail_id'])->alias('produceGold');
    }
}
