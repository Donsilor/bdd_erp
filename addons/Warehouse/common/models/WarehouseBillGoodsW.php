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
            [['id','adjust_status','status'], 'integer'],
        ];
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'adjust_status' => '目标仓库',
            'status' => '状态',
        ];
    }
}
