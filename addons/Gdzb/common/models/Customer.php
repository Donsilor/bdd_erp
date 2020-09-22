<?php

namespace addons\Gdzb\common\models;

use addons\Sales\common\models\CustomerSources;
use addons\Sales\common\models\SaleChannel;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "gdzb_customer".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户id
 * @property string $firstname 名
 * @property string $lastname 姓
 * @property string $realname 真实姓名
 * @property int $channel_id 归属渠道
 * @property int $source_id 客户来源
 * @property int $age 年龄
 * @property int $gender 性别[0:未知;1:男;2:女]
 * @property int $marriage 婚姻 1已婚 2未婚 0保密
 * @property string $wechat 微信
 * @property string $qq qq
 * @property string $mobile 手机号码
 * @property string $email 邮箱
 * @property int $country_id 所属国家
 * @property int $province_id 省
 * @property int $city_id 城市
 * @property int $area_id 地区
 * @property string $address 详细地址
 * @property int $level 客户等级
 * @property string $remark 备注
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $follower_id 归属客服
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Customer extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('customer');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'channel_id', 'source_id', 'age', 'gender', 'marriage', 'country_id','order_num',
                'province_id', 'city_id', 'area_id', 'level', 'status', 'follower_id', 'creator_id',
                'created_at', 'updated_at'], 'integer'],
            [['order_amount'], 'number',],
            [['firstname', 'lastname'], 'string', 'max' => 100],
            [['realname'], 'string', 'max' => 200],
            [['realname','wechat','channel_id'], 'required'],
            [['customer_no','wechat', 'qq', 'mobile'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 150],
            [['address', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '商户id',
            'customer_no' => '客户编号',
            'firstname' => '名',
            'lastname' => '姓',
            'realname' => '真实姓名',
            'channel_id' => '归属渠道',
            'source_id' => '客户来源',
            'age' => '年龄',
            'gender' => '性别',
            'marriage' => '婚姻',
            'wechat' => '微信',
            'qq' => 'qq',
            'mobile' => '手机号码',
            'email' => '邮箱',
            'country_id' => '所属国家',
            'province_id' => '省',
            'city_id' => '城市',
            'area_id' => '地区',
            'order_num' => '交易单数',
            'order_amount' => '交易订单总金额',
            'address' => '详细地址',
            'level' => '客户等级',
            'remark' => '备注',
            'status' => '状态',
            'follower_id' => '归属客服',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if(isset(Yii::$app->user)) {
                $this->creator_id = Yii::$app->user->identity->getId();
            }else{
                $this->creator_id = 0;
            }
        }
        return parent::beforeSave($insert);
    }


    /**
     * 销售渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('saleChannel');
    }

    /**
     * 客户来源 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(CustomerSources::class, ['id'=>'source_id'])->alias('source');
    }

    /**
     * 国家 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(\common\models\common\Country::class, ['id'=>'country_id'])->alias('country');
    }
    /**
     * 省份 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(\common\models\common\Country::class, ['id'=>'province_id'])->alias('province');
    }
    /**
     * 城市/区 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(\common\models\common\Country::class, ['id'=>'city_id'])->alias('city');
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }

    /**
     * 对应跟进人（管理员）模型
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(Member::class, ['id'=>'follower_id'])->alias('follower');
    }
}
