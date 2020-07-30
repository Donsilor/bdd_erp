<?php

namespace addons\Style\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "style_gift".
 *
 * @property int $id ID
 * @property string $style_sn 款号
 * @property string $gift_name 赠品名称
 * @property string $sale_price 销售价
 * @property int $channel_id 销售渠道
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用
 * @property int $sort 排序
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class StyleGift extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('style_gift');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_sn'], 'required'],
            [['sale_price'], 'number'],
            [['channel_id', 'auditor_id', 'audit_status', 'audit_time', 'status', 'sort', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['style_sn'], 'string', 'max' => 30],
            [['gift_name'], 'string', 'max' => 100],
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
            'style_sn' => '款号',
            'gift_name' => '赠品名称',
            'sale_price' => '销售价',
            'channel_id' => '销售渠道',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '备注',
            'status' => '状态',
            'sort' => '排序',
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
