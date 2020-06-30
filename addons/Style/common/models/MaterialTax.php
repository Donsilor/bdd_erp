<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "style_gold_loss_rate".
 *
 * @property int $id
 * @property int $style_cate_id 款式分类
 * @property int $material_type 材质ID
 * @property string $loss_rate 金损率(%)
 * @property int $creator_id 配置人ID
 * @property int $sort
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class MaterialTax extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("material_tax");
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_type'], 'required'],
            [['creator_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['tax_rate'], 'number'],
            [['material_type'], 'string','max'=>10],
            ['material_type','unique'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_type' => '材质',
            'tax_rate' => '税点(%)',
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
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'creator_id'])->alias('member');
    }
}
