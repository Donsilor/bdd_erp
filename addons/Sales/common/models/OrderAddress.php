<?php

namespace addons\Sales\common\models;

use Yii;
use common\helpers\RegularHelper;

/**
 * This is the model class for table "sales_order_address".
 *
 * @property int $order_id 订单ID
 * @property int $customer_id 客户id
 * @property int $country_id 国家ID
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property string $firstname 名字
 * @property string $lastname 姓氏
 * @property string $realname 全称
 * @property string $country_name
 * @property string $province_name
 * @property string $city_name
 * @property string $address_details 详细地址
 * @property string $zip_code 邮编
 * @property string $mobile 手机号码
 * @property string $mobile_code 手机区号
 * @property string $email 邮箱地址
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class OrderAddress extends \addons\Sales\common\models\BaseModel
{
    public $language = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_address');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id','realname','mobile','country_id','province_id','address_details'], 'required'],
            [['order_id', 'customer_id', 'country_id', 'province_id', 'city_id', 'created_at', 'updated_at'], 'integer'],
            [['firstname', 'lastname', 'city_name'], 'string', 'max' => 100],
            [['realname'], 'string', 'max' => 200],
            [['country_name', 'province_name'], 'string', 'max' => 60],
            [['address_details'], 'string', 'max' => 300],
            [['zip_code', 'mobile'], 'string', 'max' => 20],
            [['mobile_code'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 150],
            ['email', 'match', 'pattern' => RegularHelper::email(), 'message' => '邮箱地址不合法'],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'customer_id' => '客户',
            'country_id' => '国家',
            'province_id' => '省份',
            'city_id' => '城市',
            'firstname' => '名字',
            'lastname' => '姓氏',
            'realname' => '收货人',
            'country_name' => '国家',
            'province_name' => '省份',
            'city_name' => '城市',
            'address_details' => '详细地址',
            'zip_code' => '邮编',
            'mobile' => '手机号码',
            'mobile_code' => '手机区号',
            'email' => '邮箱地址',
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
        if($this->language == null){
            $this->language = $this->order->language ?? null;
        }
        //更新地区名称
        if($this->country_id > 0) {
            $this->country_name = Yii::$app->area->name($this->country_id,$this->language);
        }
        if($this->province_id > 0) {
            $this->province_name = Yii::$app->area->name($this->province_id,$this->language);
        }
        if($this->city_id > 0) {
            $this->city_name = Yii::$app->area->name($this->city_id,$this->language);
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * 对应订单商品信息模型
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class,['id'=>'order_id']);
    }
    /**
     * 客户信息 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class,['id'=>'customer_id']);
    }
}
