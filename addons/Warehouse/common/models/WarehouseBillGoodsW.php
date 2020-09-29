<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_goods_w".
 *
 * @property int $id
 * @property int $adjust_status 调整状态
 * @property int $status 状态 1已盘点 0未盘点
 */
class WarehouseBillGoodsW extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_goods_w');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','should_num','actual_num','adjust_status','status','created_at','updated_at'], 'integer'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'should_num' => '应盘数量',
            'actual_num' => '实盘数量',
            'adjust_status' => '调整状态',
            'status' => '盘点状态',
        ];
    }
}
