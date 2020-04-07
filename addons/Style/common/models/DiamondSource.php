<?php

namespace addons\Style\common\models;

use Yii;

/**
 * This is the model class for table "{{%goods_diamond_source}}".
 *
 * @property int $id
 * @property string $name 来源名称
 * @property string $website 来源网址
 * @property int $status 状态：1启动 0禁用
 * @property int $created_at
 * @property int $updated_at
 */
class DiamondSource extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("diamond_source");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'created_at', 'sort','updated_at'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['website'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('goods_diamond_source', 'ID'),
            'name' => '来源名称',
            'website' => '来源网址',
            'status' => '状态',
            'sort' => '排序',
            'created_at' => Yii::t('goods_diamond_source', '创建时间'),
            'updated_at' => Yii::t('goods_diamond_source', '更新时间'),
        ];
    }
}
