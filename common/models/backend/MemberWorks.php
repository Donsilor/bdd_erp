<?php

namespace common\models\backend;

use common\models\base\BaseModel;
use common\models\common\Department;
use Yii;

/**
 * This is the model class for table "backend_member_works".
 *
 * @property int $id ID
 * @property int $type 总结类型 1日总结 2周总结 3月总结 （枚举：WorkLogType）
 * @property string $title 标题
 * @property string $files 附件
 * @property string $content 内容
 * @property string $date 总结日期
 * @property int $creator_id 创建人
 * @property int $created_at
 * @property int $updated_at
 */
class MemberWorks extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('backend_member_works');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','content','date'], 'required'],
            [['type', 'creator_id', 'dept_id','created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['date'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['files'], 'string', 'max' => 1000],
            [['type','date','creator_id'],'unique','targetAttribute' => [ 'type','date','creator_id'],'comboNotUnique'=>'当天总结已经存在'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '总结类型',
            'title' => '标题',
            'files' => '附件',
            'content' => '内容',
            'date' => '日报日期',
            'creator_id' => '汇报人',
            'dept_id' => '部门',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
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
     * 部门一对一
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id'=>'dept_id'])->alias('department');
    }
}
