<?php

namespace addons\Style\common\models;

use common\enums\ConfirmEnum;
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
class StyleImages extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_images");
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id','image','type','position'], 'required'],
            [[ 'style_id','type','is_default','position', 'creator_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['image'], 'string', 'max' => 100],
//            [['style_id','type','position'],'unique','targetAttribute' => ['type', 'style_id','position'],'comboNotUnique'=>'已经存在']

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
            'image' => '图片',
            'type' => '图片类型',
            'position' => '位置',
            'creator_id' => '配置人',
            'is_default' => '是否默认',
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
            if(isset(Yii::$app->user)) {
                $this->creator_id = Yii::$app->user->identity->getId();
            }else {
                $this->creator_id = 1;
            }
            //如果第一次添加，则强制默认为第一张
            $style_image = self::find()->where(['style_id'=>$this->style_id])->all();
            if(empty($style_image)) $this->is_default = ConfirmEnum::YES;
        }

        if($this->is_default == ConfirmEnum::YES){
            self::updateAll(['is_default'=>ConfirmEnum::NO],['style_id'=>$this->style_id]);
            Style::updateAll(['style_image'=>$this->image],['id'=>$this->style_id]);
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
