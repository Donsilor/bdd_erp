<?php

namespace addons\style\common\models;

use Yii;
use common\models\base\BaseModel;

/**
 * This is the model class for table "goods_style_markup".
 *
 * @property int $id ID
 * @property int $style_id 款式ID
 * @property int $area_id 地区ID
 * @property string $base_price 基础销售价
 * @property string $sale_price 加价销售价
 * @property double $markup_rate 加价率
 * @property string $markup_value 规定值
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class StyleMarkup extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{%style_markup}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id'], 'required'],
            [['style_id', 'area_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['base_price','sale_price', 'markup_rate', 'markup_value'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'style_id' => '款式ID',
            'area_id' => '地区ID',
            'base_price' => '基础销售价',
            'sale_price' => '加价销售价',
            'markup_rate' => '加价率',
            'markup_value' => '固定值',
            'status' => '状态 1启用 0禁用 -1删除',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
