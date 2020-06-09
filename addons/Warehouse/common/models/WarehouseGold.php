<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_gold".
 *
 * @property int $id
 * @property string $gold_name 金料名称
 * @property string $gold_type 金料类型
 * @property int $gold_num 金料数量
 * @property string $gold_weight 库存重量
 * @property string $cost_price 成本价/克
 * @property string $sale_price 销售价格/克
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
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
            [['gold_name', 'gold_type'], 'required'],
            [['id', 'gold_weight', 'cost_price', 'sale_price'], 'number'],
            [['gold_num', 'status', 'created_at', 'updated_at'], 'integer'],
            [['gold_name'], 'string', 'max' => 30],
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
            'gold_name' => '金料名称',
            'gold_type' => '金料类型',
            'gold_num' => '金料数量',
            'gold_weight' => '库存重量',
            'cost_price' => '成本价/克',
            'sale_price' => '销售价格/克',
            'remark' => '备注',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
