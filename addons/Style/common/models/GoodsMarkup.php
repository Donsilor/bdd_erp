<?php

namespace addons\style\common\models;

use Yii;
use common\models\base\BaseModel;
use Money\Number;

/**
 * This is the model class for table "goods_markup".
 *
 * @property int $goods_id 商品ID
 * @property int $area_id 地区ID
 * @property int $markup_id 加价率ID
 * @property Number $markup_rate 加价率
 * @property Number $markup_value 固定值
 * @property Number $base_price 基础销售价
 * @property Number $sale_price 加价销售价
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at
 * @property int $updated_at
 */
class GoodsMarkup extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{%goods_markup}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'area_id'], 'required'],
            [['goods_id', 'area_id', 'markup_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['base_price','sale_price','markup_rate','markup_value'], 'number'],
            [['goods_id', 'area_id'], 'unique', 'targetAttribute' => ['goods_id', 'area_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品ID',
            'area_id' => '地区ID',
            'markup_id' => '加价率ID',
            'markup_rate' => '加价率',
            'markup_value' => '固定值',
            'base_price' => '基础销售价',
            'sale_price' => '加价销售价',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 对应款式模型
     * @return \yii\db\ActiveQuery
     */
    public function getStyleMarkup()
    {
        return $this->hasOne(StyleMarkup::class, ['id'=>'markup_id']);
    }
}
