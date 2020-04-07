<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_diamond_lang}}".
 *
 * @property int $id 商品公共表id
 * @property int $master_id 款式ID
 * @property string $language 语言类型
 * @property string $goods_name 款式名称
 * @property string $goods_body
 * @property string $mobile_body 手机端商品描述
 * @property string $meta_title SEO标题
 * @property string $meta_word SEO关键词
 * @property string $meta_desc SEO描述
 */
class DiamondLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("diamond_lang");
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
            [['goods_name'],'required'],
            [['master_id'], 'integer'],
            [['goods_body', 'mobile_body'], 'string'],
            [['language'], 'string', 'max' => 5],
            [['goods_name'], 'string', 'max' => 50],
            [['meta_title', 'meta_word'], 'string', 'max' => 255],
            [['meta_desc'], 'string', 'max' => 500],
            [['goods_desc'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'master_id' =>'Style ID',
            'language' => Yii::t('common', '语言类型'),
            'goods_name' =>Yii::t('goods', '商品名称'),
            'goods_desc' =>Yii::t('goods', '商品简介'),
            'goods_body' =>Yii::t('goods', '商品详情'),
            'mobile_body'=> Yii::t('goods', '商品详情(Wap)'),
            'meta_title' =>Yii::t('common', 'SEO标题'),
            'meta_word' =>Yii::t('common', 'SEO关键词'),
            'meta_desc' =>Yii::t('common', 'SEO描述'),
        ];
    }
}
