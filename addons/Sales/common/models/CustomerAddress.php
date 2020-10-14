<?php

namespace addons\Sales\common\models;

use common\models\backend\Member;
use Yii;
use common\enums\ConfirmEnum;

/**
 * This is the model class for table "sales_customer_address".
 *
 * @property int $id
 * @property int $customer_id 用户id
 * @property int $country_id 国家ID
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property string $firstname 名字
 * @property string $lastname 姓氏
 * @property string $realname 收货人
 * @property string $country_name 国家
 * @property string $province_name 省份
 * @property string $city_name 城市
 * @property string $address_details 详细地址
 * @property string $zip_code 邮编
 * @property string $mobile 手机号码
 * @property string $email 邮箱地址
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class CustomerAddress extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('customer_address');
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['realname','country_id','address_details'], 'required'],
                [['is_default','customer_id', 'country_id', 'province_id', 'city_id', 'created_at', 'updated_at'], 'integer'],
                [['firstname', 'lastname', 'city_name'], 'string', 'max' => 100],
                [['realname'], 'string', 'max' => 200],
                [['country_name', 'province_name'], 'string', 'max' => 60],
                [['address_details'], 'string', 'max' => 300],
                [['zip_code', 'mobile'], 'string', 'max' => 20],
                [['email'], 'string', 'max' => 150],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'customer_id' => '用户id',
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
                'mobile' => '手机',
                'email' => '邮箱',
                'is_default' => '是否默认',
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

        $count = self::find()->where(['customer_id'=>$this->customer_id,'is_default'=>1])->count();
        if($count == 0) {
            $this->is_default = ConfirmEnum::YES;
        }
        if($this->is_default == ConfirmEnum::YES){
            self::updateAll(['is_default'=>ConfirmEnum::NO],['customer_id'=>$this->customer_id]);
        }
        
        //更新地区名称
        if($this->country_id > 0) {
            $this->country_name = Yii::$app->area->name($this->country_id);
        }
        if($this->province_id > 0) {
            $this->province_name = Yii::$app->area->name($this->province_id);
        }
        if($this->city_id > 0) {
            $this->city_name = Yii::$app->area->name($this->city_id);
        }
        
        return parent::beforeSave($insert);
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
