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
class GoldPrice extends BaseModel
{
    public $refer_price;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("common_gold_price");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'status', 'created_at', 'updated_at','sync_type','sync_time','notice_status'], 'integer'],
            [['name', 'code'], 'required'],
            [['price', 'usd_price', 'rmb_rate','notice_range'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 5],
            [['notice_users'], 'implodeArray'],
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
            'price' => '金价(元/克)',
            'refer_price'=>'参考金价',    
            'usd_price' => '美元金价',
            'rmb_rate' => '人民币汇率',
            'status' => '状态',
            'sync_type'=>'同步方式',
            'sync_time'=>'同步时间',
            'sync_remark'=>'同步备注',
            'notice_status'=>'是否短信通知',
            'notice_range'=>'预警差价',
            'notice_users'=>'短信通知用户',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
    
    
}
