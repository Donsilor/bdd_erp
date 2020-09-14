<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use common\models\base\BaseModel;
use function GuzzleHttp\Psr7\str;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "warehouse_goods_log".
 *
 * @property int $id
 * @property string $goods_id 货号
 * @property int $log_type 操作类型
 * @property string $log_msg 日志信息
 * @property int $creator_id 操作人
 * @property int $created_at 操作时间
 */
class WarehouseGoodsLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_goods_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'log_msg'], 'required'],
            [['log_type', 'creator_id', 'created_at','goods_status'], 'integer'],
            [['goods_id','creator'], 'string', 'max' => 30],
            [['log_msg'], 'string', 'max' => 255],
        ];
    }

    public function beforeValidate()
    {
        $this->goods_id = (string)$this->goods_id;
        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '货号',
            'log_type' => '操作类型',
            'log_msg' => '日志信息',
            'goods_status' => '商品状态',
            'creator_id' => '操作人',
            'creator' => '操作人',
            'created_at' => '操作时间',
        ];
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
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
            $this->creator_id = Yii::$app->user->id;
            $this->creator = \Yii::$app->user->identity->username;
        }
        return parent::beforeSave($insert);
    }
    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id']);
    }
}
