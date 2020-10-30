<?php

namespace common\models\common;

use Yii;
use common\models\base\BaseModel;
use common\models\backend\Member;

/**
 * This is the model class for table "common_pend".
 *
 * @property int $id ID
 * @property int $oper_type 业务类型
 * @property int $oper_id 编号ID
 * @property string $oper_sn 业务编号
 * @property int $operor_id 业务操作人
 * @property int $pend_module 处理模块
 * @property int $pend_status 处理状态
 * @property int $pend_time 处理时间
 * @property string $pend_way 处理方式
 * @property int $flow_id 流程ID
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Pend extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('common_pend');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['oper_type', 'oper_id', 'operor_id', 'pend_module', 'pend_status', 'pend_time', 'flow_id', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['oper_sn', 'pend_way'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oper_type' => '业务类型',
            'oper_id' => '编号ID',
            'oper_sn' => '业务编号',
            'operor_id' => '待操作人',
            'pend_module' => '业务模块',
            'pend_status' => '处理状态',
            'pend_time' => '处理时间',
            'pend_way' => '处理结果',
            'flow_id' => '审批流程ID',
            'status' => '状态',
            'creator_id' => '发起人',
            'created_at' => '发起时间',
            'updated_at' => '更新时间',
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
     * 待处理人
     * @return \yii\db\ActiveQuery
     */
    public function getOperor()
    {
        return $this->hasOne(Member::class, ['id'=>'operor_id'])->alias('operor');
    }
}
