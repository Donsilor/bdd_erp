<?php

namespace common\models\common;

use common\models\backend\Member;
use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "common_flow".
 *
 * @property int $id
 * @property string $flow_name 流程名称
 * @property int $cate 流程分类
 * @property int $flow_type 流程类型
 * @property int $flow_method 流程方式
 * @property int $target_id 目标ID
 * @property string $target_no 目标编号
 * @property int $target_type 目标类型
 * @property int $flow_detail_id 最新审批记录
 * @property int $flow_status 流程状态
 * @property int $flow_total 总人数
 * @property int $flow_num 已审批人数
 * @property string $flow_remark 流程备注
 * @property int $creator_id 发起人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Flow extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("common_flow");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cate', 'flow_type', 'flow_method', 'target_id','flow_detail_id', 'target_type',  'flow_status', 'flow_total', 'flow_num', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['current_users','flow_name', 'flow_remark','url'], 'string', 'max' => 255],
            [['target_no'], 'string', 'max' => 30],
            [['id','sid'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => 'ID',
            'flow_name' => '流程名称',
            'cate' => '流程分类',
            'flow_type' => '流程类型',
            'flow_method' => '流程方式',
            'target_id' => '目标ID',
            'flow_detail_id' => '当前明细ID',
            'target_no' => '目标编号',
            'target_type' => '目标类型',
            'current_users' => '当前审批人',
            'flow_status' => '流程状态',
            'flow_total' => '总人数',
            'flow_num' => '已审批人数',
            'flow_remark' => '流程备注',
            'creator_id' => '发起人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'url' => '链接',
        ];
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
     * 部门
     * @return \yii\db\ActiveQuery
     */
    public function getFlowType()
    {
        return $this->hasOne(FlowType::class, ['id'=>'flow_type'])->alias('flowType');
    }

}
