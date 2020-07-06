<?php

namespace addons\Sales\common\models;

use Yii;
use common\helpers\RegularHelper;

/**
 * This is the model class for table "sales_customer".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户id
 * @property string $firstname 名
 * @property string $lastname 姓
 * @property string $realname 真实姓名
 * @property int $source 客户来源 (1BDD官网,2KAD官网)
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
 * @property int $status 状态[-1:删除;0:禁用;1启用]
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
            [['merchant_id', 'source', 'gender', 'marriage', 'country_id', 'province_id', 'city_id', 'area_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['birthday'], 'safe'],
            [['firstname', 'lastname'], 'string', 'max' => 100],
            [['realname'], 'string', 'max' => 200],
            [['head_portrait', 'google_account', 'facebook_account', 'email'], 'string', 'max' => 150],
            [['qq', 'mobile', 'home_phone'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户',
            'firstname' => '名',
            'lastname' => '姓',
            'realname' => '真实姓名',
            'source' => '客户来源',
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
            'city_id' => '城市/地区',
            'area_id' => '地区',
            'address' => '详细地址',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
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

}
