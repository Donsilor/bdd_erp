<?php

namespace addons\Purchase\common\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "purchase_apply".
 *
 * @property int $id
 * @property string $apply_sn 申请单号
 * @property string $total_cost 总成本
 * @property int $total_num 总数量
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态：0待审核 1通过 2不通过
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $status 状态
 * @property int $apply_status 订单状态
 * @property string $remark 采购备注
 * @property int $follower_id 跟单人
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseApply extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_apply');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_cost'], 'number'],
            [['total_num', 'auditor_id', 'audit_status', 'audit_time', 'status', 'apply_status', 'follower_id', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['apply_sn'], 'string', 'max' => 30],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'apply_sn' => Yii::t('app', '申请单号'),
            'total_cost' => Yii::t('app', '总成本'),
            'total_num' => Yii::t('app', '总数量'),
            'auditor_id' => Yii::t('app', '审核人'),
            'audit_status' => Yii::t('app', '审核状态：0待审核 1通过 2不通过'),
            'audit_time' => Yii::t('app', '审核时间'),
            'audit_remark' => Yii::t('app', '审核备注'),
            'status' => Yii::t('app', '状态'),
            'apply_status' => Yii::t('app', '订单状态'),
            'remark' => Yii::t('app', '采购备注'),
            'follower_id' => Yii::t('app', '跟单人'),
            'creator_id' => Yii::t('app', '创建人'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }
}
