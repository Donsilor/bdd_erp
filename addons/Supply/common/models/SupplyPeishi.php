<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_peishi".
 *
 * @property int $id id主键
 * @property int $produce_id 布产id
 * @property string $order_sn 订单号
 * @property string $color 钻石颜色
 * @property string $clarity 钻石净度
 * @property string $shape 钻石形状
 * @property string $cert_type 证书类型
 * @property string $cert_no 证书号
 * @property string $carat 钻石大小
 * @property int $stone_num 钻石数量(布产商品数量*钻石粒数)
 * @property string $stone_cat 钻石类型
 * @property int $stone_position 钻石位置 0：主石 ，1：副石1，2：副石2，3：副石3
 * @property int $caigou_time 采购时间（记录最新的一次采购时间）
 * @property int $songshi_time 已送生产部时间(已送生产部的最新一次时间)
 * @property int $peishi_time 配石中时间（操作配石中的最新时间）
 * @property string $caigou_user 采购人（操作采购中的人员）
 * @property string $songshi_user 送石人（已送生产部操作人员）
 * @property string $remark 配石备注
 * @property string $peishi_user 配石人（配石中操作人员）
 * @property int $peishi_status 配石状态（需建数据字典）
 * @property int $creator_id 创建人ID
 * @property string $creator_name 创建人
 * @property int $created_at 添加时间
 * @property int $updated_at
 */
class SupplyPeishi extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('supply_peishi');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produce_id', 'stone_num', 'stone_position', 'caigou_time', 'songshi_time', 'peishi_time', 'peishi_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['order_sn', 'caigou_user', 'songshi_user', 'peishi_user', 'creator_name'], 'string', 'max' => 30],
            [['color', 'clarity', 'shape', 'cert_type', 'cert_no', 'carat', 'stone_cat'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id主键'),
            'produce_id' => Yii::t('app', '布产id'),
            'order_sn' => Yii::t('app', '订单号'),
            'color' => Yii::t('app', '钻石颜色'),
            'clarity' => Yii::t('app', '钻石净度'),
            'shape' => Yii::t('app', '钻石形状'),
            'cert_type' => Yii::t('app', '证书类型'),
            'cert_no' => Yii::t('app', '证书号'),
            'carat' => Yii::t('app', '钻石大小'),
            'stone_num' => Yii::t('app', '钻石数量(布产商品数量*钻石粒数)'),
            'stone_cat' => Yii::t('app', '钻石类型'),
            'stone_position' => Yii::t('app', '钻石位置 0：主石 ，1：副石1，2：副石2，3：副石3'),
            'caigou_time' => Yii::t('app', '采购时间（记录最新的一次采购时间）'),
            'songshi_time' => Yii::t('app', '已送生产部时间(已送生产部的最新一次时间)'),
            'peishi_time' => Yii::t('app', '配石中时间（操作配石中的最新时间）'),
            'caigou_user' => Yii::t('app', '采购人（操作采购中的人员）'),
            'songshi_user' => Yii::t('app', '送石人（已送生产部操作人员）'),
            'remark' => Yii::t('app', '配石备注'),
            'peishi_user' => Yii::t('app', '配石人（配石中操作人员）'),
            'peishi_status' => Yii::t('app', '配石状态（需建数据字典）'),
            'creator_id' => Yii::t('app', '创建人ID'),
            'creator_name' => Yii::t('app', '创建人'),
            'created_at' => Yii::t('app', '添加时间'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
