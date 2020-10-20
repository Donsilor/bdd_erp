<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_l".
 *
 * @property int $id 单据ID
 * @property string $total_factory_cost 工厂成本总计
 * @property string $total_pure_gold 折足重总计
 * @property int $is_plain_gold 是否素金
 */
class WarehouseBillL extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_l');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'is_plain_gold'], 'integer'],
            [['total_factory_cost', 'total_pure_gold'], 'number'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '单据ID',
            'total_factory_cost' => '工厂成本总计',
            'total_pure_gold' => '折足重总计',
            'is_plain_gold' => '是否素金',
        ];
    }
}
