<?php

namespace addons\Style\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "style_gold".
 *
 * @property int $id ID
 * @property string $gold_type 金料材质
 * @property string $gold_name 金料名称
 * @property string $style_sn 款号
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
class GoldStyle extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('gold');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_sn'], 'unique'],
            [['gold_type', 'style_sn', 'gold_name'], 'required'],
            [['auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gold_type'], 'string', 'max' => 10],
            [['style_sn'], 'string', 'max' => 30],
            [['gold_name'], 'string', 'max' => 100],
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
            'gold_name' => '金料名称',
            'gold_type' => '金料材质',
            'style_sn' => '款号',
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
