<?php

namespace addons\Style\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "style_style_channel".
 *
 * @property int $id
 * @property int $merchant_id 商户ID
 * @property string $name 渠道名称
 * @property string $code 渠道标签
 * @property string $tag 渠道标签
 * @property int $status 状态
 * @property int $creator_id 创建人ID
 * @property int $created_at
 * @property int $updated_at
 */
class StyleChannel extends \addons\Sales\common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('sale_channel');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['merchant_id', 'status','audit_status','auditor_id', 'creator_id', 'created_at','audit_time', 'sort','updated_at'], 'integer'],
            [['tag'], 'string', 'max' => 10],
            [['code'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100],
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
            'merchant_id' => '商户',
            'name' => '渠道名称',
            'code' => '渠道编码',
            'tag' => '标签(编款用)',
            'status' => '状态',
            'sort' => '排序',
            'audit_status' => '审核状态',
            'audit_remark' => '审核备注',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
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
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
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
