<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_j".
 *
 * @property int $id 单据ID
 * @property int $lender_id 借货人
 * @property int $lend_status 借货状态
 * @property int $restore_time 预计还货时间
 */
class WarehouseBillJ extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_j');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'lender_id', 'lend_status', 'restore_time'], 'integer'],
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
            'lender_id' => '借货人',
            'lend_status' => '借货状态',
            'restore_time' => '预计还货时间',
        ];
    }
}
