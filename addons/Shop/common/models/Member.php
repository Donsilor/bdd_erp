<?php

namespace addons\Shop\common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\helpers\RegularHelper;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property int $id 主键
 * @property string $merchant_id 商户id
 * @property string $username 帐号
 * @property string $password_hash 密码
 * @property string $auth_key 授权令牌
 * @property string $password_reset_token 密码重置令牌
 * @property int $type 类别[1:普通会员;10管理员]
 * @property string $nickname 昵称
 * @property string $realname 真实姓名
 * @property string $firstname 真实姓名
 * @property string $lastname 真实姓名
 * @property string $head_portrait 头像
 * @property int $gender 性别[0:未知;1:男;2:女]
 * @property string $qq qq
 * @property string $email 邮箱
 * @property string $birthday 生日
 * @property string $visit_count 访问次数
 * @property string $home_phone 家庭号码
 * @property string $mobile 手机号码
 * @property int $role 权限
 * @property int $last_time 最后一次登录时间
 * @property string $last_ip 最后一次登录ip
 * @property int $country_id 国家id
 * @property int $province_id 省
 * @property int $city_id 城市
 * @property int $area_id 地区
 * @property int $country 国家名称
 * @property int $city 城市名称
 * @property string $pid 上级id
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 * @property string $is_tourist 是否游客
 */
class Member extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("member");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required', 'on' => ['backendCreate']],
            [['password_hash'], 'string', 'min' => 6, 'on' => ['backendCreate']],
            [['username'], 'unique', 'on' => ['backendCreate']],
            [['is_tourist', 'marriage','merchant_id', 'type', 'gender','visit_count', 'role', 'last_time','country_id', 'province_id', 'city_id', 'area_id', 'pid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['birthday','created_at','is_book','is_buy'], 'safe'],
            [[ 'qq', 'home_phone', 'mobile'], 'string', 'max' => 20],
            [['password_hash', 'password_reset_token', 'head_portrait'], 'string', 'max' => 150],
            [['auth_key'], 'string', 'max' => 32],
            [['first_ip_location'], 'string', 'max' => 500],
            [['nickname'], 'string', 'max' => 120],
            [['realname'], 'string', 'max' => 200],
            [['google_account', 'facebook_account'], 'string', 'max' => 150],
            [['username','firstname','lastname'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 150],
            [['last_ip','first_ip'], 'string', 'max' => 16],
            ['mobile', 'match', 'pattern' => RegularHelper::chinaMobile(),'message' => '不是一个有效的手机号码'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'username' => '账号',
            'password_hash' => '密码',
            'auth_key' => '授权登录key',
            'password_reset_token' => '密码重置token',
            'type' => '类型',
            'nickname' => '昵称',
            'realname' => '真实姓名',
            'lastname' => '姓氏',
            'firstname' => '名子',
            'head_portrait' => '头像',
            'gender' => '性别',
            'qq' => 'QQ',
            'email' => '邮箱',
            'birthday' => '生日',
            'visit_count' => '登录总次数',
            'home_phone' => '家庭号码',
            'mobile' => '手机号码',
            'role' => '权限',
            'last_time' => '最后一次登录时间',                
            'last_ip' => 'ip',
            'first_ip_location' => '注册IP地址',
            'first_ip' => '注册IP',
            'country_id' => '国家',
            'province_id' => '省',
            'city_id' => '市',
            'area_id' => '区',
            'pid' => '上级id',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'marriage' => '婚姻',
            'google_account' => 'Google账户',
            'facebook_account' => 'Facebook账户',
            'is_tourist' => '是否游客'
        ];
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['merchant_id'],
                ],
                'value' => Yii::$app->services->merchant->getId(),
            ]
        ];
    }
}
