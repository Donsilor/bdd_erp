<?php

namespace addons\Purchase\common\models;


use Yii;
use addons\Supply\common\models\Supplier;
use common\models\backend\Member;

/**
 * This is the model class for table "purchase_defective".
 *
 * @property int $id ID
 * @property int $merchant_id 商户ID
 * @property string $defective_no 返厂单编号
 * @property int $supplier_id 供应商ID
 * @property string $receipt_no 工厂出货单号
 * @property int $defective_num 货品数量
 * @property string $total_const 总金额
 * @property int $auditor_id 审核人
 * @property int $audit_time 审核时间
 * @property int $audit_status 审核状态
 * @property string $audit_remark 审核备注
 * @property string $remark 单据备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $creator_id 制单人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseDefective extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_defective');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'supplier_id', 'defective_num', 'auditor_id', 'audit_time', 'audit_status', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['supplier_id', 'receipt_no'], 'required'],
            [['total_cost'], 'number'],
            [['defective_no', 'receipt_no'], 'string', 'max' => 30],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'defective_no' => '返厂单编号',
            'supplier_id' => '供应商',
            'receipt_no' => '工厂出货单号',
            'defective_num' => '不良货品数量',
            'total_cost' => '总金额',
            'auditor_id' => '审核人',
            'audit_time' => '审核时间',
            'audit_status' => '审核状态',
            'audit_remark' => '审核备注',
            'remark' => '单据备注',
            'sort' => '排序',
            'status' => '状态',
            'creator_id' => '制单人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
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
}
