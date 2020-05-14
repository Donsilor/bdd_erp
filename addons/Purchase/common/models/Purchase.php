<?php

namespace addons\Purchase\common\models;

use Yii;
use common\models\backend\Member;
use addons\Supply\common\models\Supplier;

/**
 * This is the model class for table "purchase".
 *
 * @property int $id
 * @property string $title
 * @property string $purchase_sn 采购单号
 * @property int $supplier_id 供应商
 * @property int $follower_id 跟单人
 * @property string $cost_total 总成本
 * @property int $goods_total 总数量
 * @property int $creator_id 创建人
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property string $audit_remark 审核备注
 * @property string $remark 采购备注
 * @property int $status 状态 1已布产 0待布产  -1删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Purchase extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cost_total'], 'number'],
            [['supplier_id'], 'required'],
            [['id','supplier_id','goods_count', 'creator_id', 'auditor_id', 'audit_status', 'status','audit_time', 'created_at', 'updated_at'], 'integer'],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
            [['purchase_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_sn' => '采购单号',
            'supplier_id' => '供应商',
            'cost_total' => '总金额(RMB)',
            'goods_count' => '总数量(件)',  
            'follower_id' => '跟单人',
            'cost_total' => '总金额',
            'goods_count' => '总数量',  
            'creator_id' => '创建人',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '采购备注',
            'status' => '状态 ',
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
     * 跟单人
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(Member::class, ['id'=>'follower_id'])->alias('follower');
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
