<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_sale_channel".
 *
 * @property int $id
 * @property int $merchant_id 商户ID
 * @property string $code 渠道编码
 * @property string $tag 标签
 * @property string $name 渠道名称
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $sort
 * @property int $creator_id 创建人ID
 * @property int $created_at
 * @property int $updated_at
 */
class SaleChannel extends BaseModel
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
            [['merchant_id', 'status', 'sort', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['code', 'tag'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
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
            'code' => '渠道编码',
            'tag' => '标签',
            'name' => '渠道名称',
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
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
    }
}
