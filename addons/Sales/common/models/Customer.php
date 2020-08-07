<?php

namespace addons\Sales\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "sales_customer".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户id
 * @property string $customer_no 客户编号
 * @property string $firstname 名
 * @property string $lastname 姓
 * @property string $realname 真实姓名
 * @property int $channel_id 归属渠道
 * @property int $source_id 客户来源
 * @property string $head_portrait 头像
 * @property int $gender 性别[0:未知;1:男;2:女]
 * @property int $marriage 婚姻 1已婚 2未婚 0保密
 * @property string $google_account google账号+
 * @property string $facebook_account facebook账号+
 * @property string $qq qq
 * @property string $mobile 手机号码
 * @property string $email 邮箱
 * @property string $birthday 生日
 * @property string $home_phone 家庭号码
 * @property int $country_id 所属国家
 * @property int $province_id 省
 * @property int $city_id 城市
 * @property int $area_id 地区
 * @property string $address 详细地址
 * @property int $age 年龄
 * @property int $level 客户等级
 * @property string $language 语言
 * @property string $currency 货币
 * @property int $is_invoice 是否默认开发票
 * @property int $invoice_type 发票类型
 * @property string $invoice_title 发票抬头
 * @property int $invoice_title_type 抬头类型
 * @property string $invoice_tax 发票税务号
 * @property string $invoice_email 接收发票邮箱
 * @property string $remark 备注
 * @property int $status 状态[-1:删除;0:禁用;1启用]
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
            [['merchant_id', 'channel_id', 'source_id', 'gender', 'marriage', 'country_id', 'province_id', 'city_id', 'area_id', 'age', 'level', 'is_invoice', 'invoice_type', 'invoice_title_type', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['firstname', 'lastname', 'invoice_title'], 'string', 'max' => 100],
            [['realname'], 'string', 'max' => 200],
            [['head_portrait', 'google_account', 'facebook_account', 'email', 'invoice_email'], 'string', 'max' => 150],
            [['qq', 'mobile', 'home_phone'], 'string', 'max' => 20],
            [['customer_no'], 'string', 'max' => 30],
            [['address', 'invoice_tax', 'remark'], 'string', 'max' => 255],
            [['language', 'currency'], 'string', 'max' => 10],
            [['birthday'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户id',
            'customer_no' => '客户编号',
            'firstname' => '名',
            'lastname' => '姓',
            'realname' => '真实姓名',
            'channel_id' => '归属渠道',
            'source_id' => '客户来源',
            'head_portrait' => '头像',
            'gender' => '性别',
            'marriage' => '婚姻',
            'google_account' => 'google账号+',
            'facebook_account' => 'facebook账号+',
            'qq' => 'qq',
            'mobile' => '手机号码',
            'email' => '邮箱',
            'birthday' => '生日',
            'home_phone' => '家庭号码',
            'country_id' => '所属国家',
            'province_id' => '省',
            'city_id' => '城市',
            'area_id' => '地区',
            'address' => '详细地址',
            'age' => '客户年龄',
            'level' => '客户级别',
            'language' => '语言',
            'currency' => '货币',
            'is_invoice' => '是否默认开发票',
            'invoice_type' => '发票类型',
            'invoice_title' => '发票抬头',
            'invoice_title_type' => '抬头类型',
            'invoice_tax' => '发票税务号',
            'invoice_email' => '接收发票邮箱',
            'remark' => '备注',
            'status' => '状态',
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
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    /*public function beforeSave($insert)
    {

        if(RegularHelper::verify('chineseCharacters',$this->lastname.''.$this->firstname)){
            $realname  = $this->lastname.''.$this->firstname;
        }else {
            $realname  = $this->firstname.' '.$this->lastname;
        }
        if(trim($realname) != '' && $realname != $this->realname){
            $this->realname = $realname;
        }

        return parent::beforeSave($insert);
    }*/

    /**
     * 销售渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
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
}
