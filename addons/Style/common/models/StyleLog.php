<?php

namespace addons\Style\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

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
class StyleLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_log");
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id','creator_id'], 'required'],
            [[ 'style_id','log_type','creator_id', 'log_time'], 'integer'],
            [['log_msg'], 'string', 'max' => 500],
            [['style_sn','creator','log_module'], 'string', 'max' => 30],

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
            'style_sn' => '款号',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'log_module'=>'操作模块',
            'creator' => '操作人',
            'creator_id' => '操作人ID',
            'log_time' => '处理时间',

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
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
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
