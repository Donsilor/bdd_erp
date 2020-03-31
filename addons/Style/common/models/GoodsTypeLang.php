<?php

namespace addons\style\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_category_lang}}".
 *
 * @property int $id 主键
 * @property int $attr_id
 * @property string $language 语言类型
 * @property string $cat_name 分类名称
 * @property string $remark 备注
 * @property string $meta_title meta标题
 * @property string $meta_desc meta描述
 * @property string $meta_word meta关键词
 */
class GoodsTypeLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{%goods_type_lang}}';
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
                [['type_name'], 'required'],
                [['master_id'], 'integer'],
                [['language'], 'string', 'max' => 5],
                [['type_name'], 'string', 'max' => 30],
                [['meta_title','type_title'], 'string', 'max' => 200],
                [['meta_desc', 'meta_word','type_desc'], 'string', 'max' => 500],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'master_id' => Yii::t('goods_type', 'Attr ID'),
                'language' => Yii::t('common', '语言类型'),
                'type_name' => Yii::t('goods_type', '产品线'),
                'type_title' => Yii::t('common', '标题'),
                'type_desc' => Yii::t('common', '描述'),
                'meta_title' => Yii::t('common', 'SEO标题'),
                'meta_desc' => Yii::t('common', 'SEO描述'),
                'meta_word' => Yii::t('common', 'SEO关键词'),
        ];
    }
}
