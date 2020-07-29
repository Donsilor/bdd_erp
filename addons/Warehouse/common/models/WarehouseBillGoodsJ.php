<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_bill_goods_j".
 *
 * @property int $id 单据明细ID
 * @property int $bill_id 单据ID
 * @property int $lend_status 借货状态
 * @property int $receive_id 接收人
 * @property int $receive_time 接收时间
 * @property string $receive_remark 接收备注
 * @property int $restore_time 还货时间
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
            [['id', 'bill_id', 'lend_status', 'receive_id', 'receive_time', 'restore_time', 'qc_status'], 'integer'],
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
            'bill_id' => '单据ID',
            'lend_status' => '借货状态',
            'receive_id' => '接收人',
            'receive_time' => '接收时间',
            'receive_remark' => '接收备注',
            'restore_time' => '还货时间',
            'qc_status' => '质检状态',
            'qc_remark' => '质检备注',
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
     * 接收人
     * @return \yii\db\ActiveQuery
     */
    public function getReceive()
    {
        return $this->hasOne(Member::class, ['id'=>'receive_id'])->alias('receive');
    }
}
