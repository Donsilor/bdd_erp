<?php

namespace addons\style\common\models;

use Yii;

/**
 * This is the model class for table "goods_lang".
 *
 * @property int $id 商品id(SKU)
 * @property int $master_id
 * @property string $language 语言类型
 * @property string $goods_name 商品名称（+规格名称）
 * @property string $goods_desc 商品广告词
 * @property string $spec_name 规格名称
 * @property string $goods_spec 商品规格序列化
 */
class GoodsLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{style_goods_lang}}';
    }    
    /**
     * behaviors
     * {@inheritDoc}
     * @see \common\models\base\BaseModel::behaviors()
     */
    public function behaviors()
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['master_id'], 'integer'],
            [['language', 'goods_name', 'spec_name', 'goods_spec'], 'required'],
            [['goods_spec'], 'string'],
            [['language'], 'string', 'max' => 5],
            [['goods_name'], 'string', 'max' => 50],
            [['goods_desc'], 'string', 'max' => 150],
            [['spec_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods', 'ID'),
            'master_id' => Yii::t('goods', 'Master ID'),
            'language' => Yii::t('goods', 'Language'),
            'goods_name' => Yii::t('goods', 'Goods Name'),
            'goods_desc' => Yii::t('goods', 'Goods Desc'),
            'spec_name' => Yii::t('goods', 'Spec Name'),
            'goods_spec' => Yii::t('goods', 'Goods Spec'),
        ];
    }
}
