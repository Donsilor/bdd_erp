<?php

namespace common\models\common;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "common_goldprice".
 *
 * @property int $id ID
 * @property int $merchant_id 商户ID
 * @property string $name 名称
 * @property string $code 代号
 * @property double $price 设置汇率
 * @property double $usd_price 美元金价
 * @property double $rmb_rate 人民币汇率
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $created_at 添加时间
 * @property int $updated_at 更新时间
 */
class Goldprice extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("common_goldprice");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'code'], 'required'],
            [['price', 'usd_price', 'rmb_rate'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'name' => '名称',
            'code' => '代号',
            'price' => '设置汇率',
            'usd_price' => '美元金价',
            'rmb_rate' => '人民币汇率',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}
