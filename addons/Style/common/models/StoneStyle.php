<?php

namespace addons\Style\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "style_stone".
 *
 * @property int $id ID
 * @property string $stone_type 石料类型
 * @property string $style_sn 款号
 * @property string $stone_weight_min 石重范围小(ct)
 * @property string $stone_weight_max 石重范围大(ct)
 * @property int $cert_type 证书类型
 * @property string $product_size_min 尺寸小(mm)
 * @property string $product_size_max 尺寸大(mm)
 * @property string $stone_shape 石头形状
 * @property string $stone_carat 石重(ct)
 * @property string $color_scope 颜色范围
 * @property string $clarity_scope 净度范围
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
class StoneStyle extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('stone');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stone_type', 'style_sn'], 'required'],
            [['stone_weight_min', 'stone_weight_max', 'stone_carat'], 'number'],
            [['cert_type', 'auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['stone_type', 'stone_shape'], 'string', 'max' => 10],
            [['style_sn', 'color_scope', 'clarity_scope'], 'string', 'max' => 30],
            [['product_size_min', 'product_size_max'], 'string', 'max' => 100],
            [['remark', 'audit_remark'], 'string', 'max' => 255],
            [['style_sn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stone_type' => '石料类型',
            'style_sn' => '款号',
            'stone_weight_min' => '石重范围小(ct)',
            'stone_weight_max' => '石重范围大(ct)',
            'cert_type' => '证书类型',
            'product_size_min' => '尺寸小(mm)',
            'product_size_max' => '尺寸大(mm)',
            'stone_shape' => '石头形状',
            'stone_carat' => '石重(ct)',
            'color_scope' => '颜色范围(D-Z)',
            'clarity_scope' => '净度范围(FL-SI2)',
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
