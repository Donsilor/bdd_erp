<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_return_check".
 *
 * @property int $id ID
 * @property int $return_id 退款ID
 * @property string $return_no 退款编号
 * @property int $leader_id 部门主管
 * @property int $leader_status 主管审核状态
 * @property string $leader_remark 主管审核意见
 * @property int $leader_time 主管审核时间
 * @property int $storekeeper_id 库管
 * @property int $storekeeper_status 库管审核
 * @property string $storekeeper_remark 库管审核备注
 * @property int $storekeeper_time 库管审核时间
 * @property int $finance_id 财务
 * @property int $finance_status 财务审核状态
 * @property string $finance_remark 财务审核备注
 * @property int $finance_time 财务审核时间
 * @property int $payer_id 实际付款人
 * @property int $pay_status 支付状态
 * @property string $pay_remark 付款备注
 * @property string $pay_receipt 付款凭证
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class SalesReturnCheck extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('sales_return_check');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['return_id', 'return_no'], 'required'],
            [['return_id', 'leader_id', 'leader_status', 'leader_time', 'storekeeper_id', 'storekeeper_status', 'storekeeper_time', 'finance_id', 'finance_status', 'finance_time', 'payer_id', 'pay_status', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['return_no'], 'string', 'max' => 30],
            [['leader_remark', 'storekeeper_remark', 'finance_remark', 'pay_remark', 'pay_receipt'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'return_id' => '退款ID',
            'return_no' => '退款编号',
            'leader_id' => '部门主管',
            'leader_status' => '主管审核状态',
            'leader_remark' => '主管审核意见',
            'leader_time' => '主管审核时间',
            'storekeeper_id' => '库管',
            'storekeeper_status' => '库管审核',
            'storekeeper_remark' => '库管审核备注',
            'storekeeper_time' => '库管审核时间',
            'finance_id' => '财务',
            'finance_status' => '财务审核状态',
            'finance_remark' => '财务审核备注',
            'finance_time' => '财务审核时间',
            'payer_id' => '实际付款人',
            'pay_status' => '支付状态',
            'pay_remark' => '付款备注',
            'pay_receipt' => '付款凭证',
            'status' => '状态 1启用 0禁用 -1删除',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
