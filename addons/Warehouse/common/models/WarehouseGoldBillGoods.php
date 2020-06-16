<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_gold_bill_goods".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property string $gold_name 金料名称
 * @property string $style_sn 金料款号
 * @property string $gold_type 商品类型
 * @property int $gold_num 金料总数
 * @property string $gold_weight 金料总重量
 * @property string $cost_price 成本价
 * @property string $gold_price 单价
 * @property string $sale_price 销售价格
 * @property int $source_detail_id 来源明细ID
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
            [['gold_weight', 'cost_price', 'gold_price', 'sale_price'], 'number'],
            [['bill_type', 'gold_type'], 'string', 'max' => 10],
            [['bill_no', 'gold_name', 'style_sn'], 'string', 'max' => 30],
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
            'bill_type' => '单类型',
            'gold_name' => '金料名称',
            'style_sn' => '金料款号',
            'gold_type' => '金料类型',
            'gold_num' => '金料总数',
            'gold_weight' => '金料总重/克',
            'cost_price' => '金料总额',
            'gold_price' => '金料单价/克',
            'sale_price' => '销售价格',
            'source_detail_id' => '来源明细ID',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
