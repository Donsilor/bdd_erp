<?php

namespace addons\Sales\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "sales_platform".
 *
 * @property int $id
 * @property string $name 平台名称
 * @property string $language 默认语言
 * @property string $currency 默认货币
 * @property int $channel_id 归属渠道
 * @property int $payment_id 支付方式
 * @property string $realname 收货人
 * @property string $mobile 收货人电话
 * @property int $country_id 国家
 * @property int $province_id 省份
 * @property int $city_id 城市
 * @property string $address_details 详细地址
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Platform extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('platform');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','type','channel_id'], 'required'],
            [['type','channel_id', 'payment_id', 'country_id', 'province_id', 'city_id','status','sort', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['language'], 'string', 'max' => 5],
            [['currency'], 'string', 'max' => 3],
            [['realname', 'mobile','code','zip_code'], 'string', 'max' => 30],
            [['address_details'], 'string', 'max' => 255],
            [['name'],'unique', 'targetAttribute'=>['name'], ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '平台代号',
            'name' => '平台名称',
            'language' => '默认语言',
            'currency' => '默认货币',
            'type' => '发货方式',
            'channel_id' => '归属渠道',
            'payment_id' => '默认支付方式',
            'realname' => '联系人',
            'mobile' => '联系电话',
            'country_id' => '国家',
            'province_id' => '省份',
            'city_id' => '城市',
            'address_details' => '详细地址',
            'zip_code' => '邮编',
            'sort' => '排序',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->creator_id = Yii::$app->user->identity->getId() ?? 0;
        }
        return parent::beforeSave($insert);
    }
    /**
     * 对应 归属渠道模型
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }
    
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
}
