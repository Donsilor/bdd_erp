<?php

namespace addons\style\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_ring_lang}}".
 *
 * @property int $id 主键ID
 * @property int $master_id 对接ID
 * @property string $language 语言类型
 * @property string $ring_name 对戒名称
 * @property string $ring_body 图文描述
 * @property string $meta_title meta标题
 * @property string $meta_desc meta描述
 * @property string $meta_word meta关键字
 */
class RingLang extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{style_ring_lang}}';
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
            [['ring_name'],'required'],
            [['master_id'], 'integer'],
            [['ring_body'], 'string'],
            [['language'], 'string', 'max' => 5],
            [['ring_name'], 'string', 'max' => 100],
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
            'master_id' => 'Ring ID',
            'language' => Yii::t("common","语言类型"),
            'ring_name' => Yii::t("goods","对戒名称"),
            'ring_body' => Yii::t("goods","对戒详情"),
            'meta_title' => Yii::t('goods', 'SEO标题'),
            'meta_word' => Yii::t('goods', 'SEO关键词'),
            'meta_desc' => Yii::t('goods', 'SEO描述'),
        ];
    }
}
