<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "common_country".
 *
 * @property int $id 主键
 * @property string $title
 * @property int $pid 父ID
 * @property string $tree 路径
 * @property int $level 层级
 * @property string $name_zh_cn 中文名称
 * @property string $name_zh_tw 繁体名称
 * @property string $name_en_us 英文名称
 * @property string $name_pinyin 中文拼音
 * @property string $code 代码
 * @property int $sort 排序
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'common_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'level', 'sort'], 'integer'],
            [['title', 'code'], 'string', 'max' => 50],
            [['tree', 'name_zh_cn', 'name_zh_tw', 'name_en_us', 'name_pinyin'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'title' => '标题',
            'pid' => '父ID',
            'tree' => '路径',
            'level' => '层级',
            'name_zh_cn' => '中文名称',
            'name_zh_tw' => '繁体名称',
            'name_en_us' => '英文名称',
            'name_pinyin' => '中文拼音',
            'code' => '代码',
            'sort' => '排序',
        ];
    }    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'pid'])->select(['id', 'title', 'pid']);
    }
}
