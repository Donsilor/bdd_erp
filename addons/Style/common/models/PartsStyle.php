<?php

namespace addons\Style\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "style_parts".
 *
 * @property int $id ID
 * @property string $style_sn 款号
 * @property string $parts_name 配件名称
 * @property string $parts_type 配件类型
 * @property string $metal_type 金属类型
 * @property string $color 配件颜色
 * @property string $shape 配件形状
 * @property string $remark 备注
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PartsStyle extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('parts');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_sn', 'parts_type'], 'required'],
            [['auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['style_sn'], 'string', 'max' => 30],
            [['parts_name'], 'string', 'max' => 100],
            [['parts_type', 'metal_type', 'color', 'shape'], 'string', 'max' => 10],
            [['remark', 'audit_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_sn' => '款号',
            'parts_name' => '配件名称',
            'parts_type' => '配件类型',
            'metal_type' => '金属材质',
            'color' => '配件颜色',
            'shape' => '配件形状',
            'remark' => '备注',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'sort' => '排序',
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
}
