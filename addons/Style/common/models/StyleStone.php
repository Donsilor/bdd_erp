<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "style_gold_loss_rate".
 *
 * @property int $id
 * @property int $style_id 款号ID
 * @property int $position 石头位置
 * @property int $stone_type 石头类型
 * @property int $creator_id 配置人ID
 * @property int $sort
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class StyleStone extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_stone");
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id','position'], 'required'],
            [[ 'style_id','position','stone_type', 'creator_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_id' => '款号ID',
            'position' => '石头位置',
            'stone_type' => '石头类型',
            'creator_id' => '配置人',
            'sort' => '排序',
            'status' => '状态',
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
     * 关联款式一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyle()
    {
        return $this->hasOne(Style::class, ['id'=>'style_id'])->alias('style');
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
