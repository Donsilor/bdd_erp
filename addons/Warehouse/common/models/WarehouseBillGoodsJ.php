<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_goods_j".
 *
 * @property int $id 单据明细ID
 * @property int $receive_status 接收状态
 * @property int $receive_id 接收人
 * @property int $receive_time 接收时间
 * @property string $receive_remark 接收备注
 * @property int $qc_status 质检状态
 * @property string $qc_remark 质检备注
 */
class WarehouseBillGoodsJ extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_goods_j');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'receive_status', 'receive_id', 'receive_time', 'qc_status'], 'integer'],
            [['receive_remark', 'qc_remark'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '单据明细ID',
            'receive_status' => '接收状态',
            'receive_id' => '接收人',
            'receive_time' => '接收时间',
            'receive_remark' => '接收备注',
            'qc_status' => '质检状态',
            'qc_remark' => '质检备注',
        ];
    }
}
