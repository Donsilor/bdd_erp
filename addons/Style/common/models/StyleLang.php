<?php

namespace addons\style\common\models;

use Yii;

/**
 * 款式语言表
 *
 * @property int $id 商品公共表id
 * @property int $master_id 款式ID
 * @property string $language 语言类型
 * @property string $style_name 款式名称
 * @property string $style_desc 商品广告词
 * @property string $style_attr 商品属性
 * @property string $style_custom 商品自定义属性
 * @property string $goods_body 商品内容
 * @property string $mobile_body 手机端商品描述
 * @property string $meta_title SEO标题
 * @property string $meta_word SEO关键词
 * @property string $meta_desc SEO描述
 */
class StyleLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_lang");
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
            [['style_name'], 'required'],
            [['language'], 'string', 'max' => 5],
            [['goods_body', 'mobile_body'], 'string'],         
            [['style_name'], 'string', 'max' => 300],
            [['style_desc'], 'string', 'max' => 1000],
            [['meta_title', 'meta_word'], 'string', 'max' => 255],
            [['meta_desc'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'master_id' => 'Master ID',
            'language' => '语言类型',
            'style_name' => '款式名称',
            'style_desc' => '款式描述',
            'goods_body' => '商品详情',
        ];
    }
}
