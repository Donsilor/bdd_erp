<?php

namespace common\models\common;

use common\models\base\BaseModel;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "common_flow_details".
 *
 * @property int $id
 * @property int $flow_id 流程ID
 * @property int $user_id 审批人
 * @property int $audit_status 审核状态
 * @property string $audit_remark 审核备注
 * @property int $audit_time
 */
class FlowDetails extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("common_flow_details");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flow_id', 'user_id'], 'required'],
            [['flow_id', 'user_id', 'audit_status', 'audit_time'], 'integer'],
            [['audit_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'flow_id' => '流程ID',
            'user_id' => '审批人',
            'audit_status' => '审核状态',
            'audit_remark' => '审核备注',
            'audit_time' => 'Audit Time',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [

        ];
    }


    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id'=>'user_id'])->alias('member');
    }
}
