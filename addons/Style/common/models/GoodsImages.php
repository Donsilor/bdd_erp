<?php

namespace addons\style\common\models;

use Yii;
use common\models\base\BaseModel;

/**
 * This is the model class for table "goods_images".
 *
 * @property int $id 商品图片id
 * @property int $style_id 款式ID
 * @property string $image_thumb 商品缩略图
 * @property string $image_middle 中图
 * @property string $image_large 大图
 * @property string $image_origin 原图
 * @property int $sort 排序
 * @property int $is_default 默认图，1是，0否
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class GoodsImages extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_images}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['style_id', 'image_thumb'], 'required'],
                [['style_id', 'sort', 'is_default', 'created_at', 'updated_at'], 'integer'],
                [['image_thumb'], 'string', 'max' => 1000],
                [['image_middle', 'image_large', 'image_origin'], 'string', 'max' => 200],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => Yii::t('goods_images', 'ID'),
                'style_id' => Yii::t('style', 'Style ID'),
                'image_thumb' => Yii::t('goods_images', 'Image Thumb'),
                'image_middle' => Yii::t('goods_images', 'Image Middle'),
                'image_large' => Yii::t('goods_images', 'Image Large'),
                'image_origin' => Yii::t('goods_images', 'Image Origin'),
                'sort' => Yii::t('common', '排序'),
                'is_default' => Yii::t('common', '是否默认'),
                'created_at' => Yii::t('common', '创建时间'),
                'updated_at' => Yii::t('common', '更新时间'),
        ];
    }
}
