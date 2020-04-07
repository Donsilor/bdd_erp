<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "style_gold_loss_rate".
 *
 * @property int $id
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $profit_rate 毛利率(%)
 * @property int $creator_id 配置人ID
 * @property int $sort
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class ProfitRate extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("profit_rate");
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_cate_id','product_type_id'], 'required'],
            [['style_cate_id','product_type_id',  'creator_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['profit_rate'], 'number'],
            [['style_cate_id','product_type_id'],'unique','targetAttribute' => ['style_cate_id', 'product_type_id'],'comboNotUnique'=>'此产品分类和产品线已经存在']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'profit_rate' => '毛利率(%)',
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
            $this->creator_id = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }


    /**
     * 关联产品分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    }

    /**
     * 关联产品线一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias('product');
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
