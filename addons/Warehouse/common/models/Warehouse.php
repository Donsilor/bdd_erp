<?php

namespace addons\Warehouse\common\models;

use addons\Style\common\models\StyleChannel;
use common\models\backend\Member;
use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $id
 * @property int $type 仓库类型
 * @property string $name 仓库名
 * @property string $code 编码
 * @property int $status 状态
 * @property int $sort 排序
 * @property int $channel_id 归属渠道
 * @property int $creator_id 添加人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Warehouse extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse');

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status', 'audit_status','sort','creator_id', 'created_at','auditor_id','channel_id', 'audit_time', 'updated_at'], 'integer'],
            [['name','code','type','channel_id'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 50],
            [['audit_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '仓库类型',
            'name' => '仓库名',
            'code' => '编码',
            'status' => '状态',
            'channel_id' => '归属渠道',
            'audit_status' => '审核状态',
            'audit_remark' => '审核备注',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
            'sort' => '排序',
            'creator_id' => '添加人',
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
            $this->creator_id = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }


    /**
     * 归属渠道
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'channel_id'])->alias('channel');
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
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }


}
