<?php

namespace addons\Warehouse\common\models;

use Yii;
use addons\Sales\common\models\SaleChannel;
use addons\Supply\common\models\Supplier;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_templet_bill".
 *
 * @property int $id ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property int $bill_status 单据状态
 * @property int $supplier_id 供应商
 * @property int $channel_id 渠道
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $account_type 结算方式
 * @property int $adjust_type 调整类型 0扣减 1增加
 * @property int $total_num 总数量
 * @property string $total_weight 总重量(g)
 * @property string $total_cost 样板总额
 * @property string $delivery_no 送货单号
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $fin_status 财务审核状态
 * @property string $fin_checker 财务确认人
 * @property int $fin_check_time 财务确认时间
 * @property string $fin_remark 财务确认备注
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseTempletBill extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_templet_bill');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['bill_no', 'bill_type', 'bill_status'], 'required'],
            [['bill_status', 'supplier_id', 'channel_id', 'put_in_type', 'to_warehouse_id', 'account_type', 'adjust_type', 'total_num', 'auditor_id', 'audit_status', 'audit_time', 'fin_status', 'fin_check_time', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['total_weight', 'total_cost'], 'number'],
            [['bill_no', 'delivery_no', 'fin_checker'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['audit_remark', 'fin_remark', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_no' => '单据编号',
            'bill_type' => '单据类型',
            'bill_status' => '单据状态',
            'supplier_id' => '供应商',
            'channel_id' => '渠道',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'account_type' => '结算方式',
            'adjust_type' => '调整类型',
            'total_num' => '总数量',
            'total_weight' => '总重量(g)',
            'total_cost' => '样板总额',
            'delivery_no' => '送货单号',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'fin_status' => '财务审核状态',
            'fin_checker' => '财务确认人',
            'fin_check_time' => '财务确认时间',
            'fin_remark' => '财务确认备注',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->creator_id = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
    /**
     * 供应商 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
    /**
     * 入库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getToWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'to_warehouse_id'])->alias('toWarehouse');
    }
    /**
     * 销售渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }
}
