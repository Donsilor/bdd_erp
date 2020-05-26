<?php

namespace addons\Warehouse\common\models;

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
            [['type', 'status', 'sort', 'is_lock','creator_id', 'created_at', 'updated_at'], 'integer'],
            [['name','code','type'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['code'], 'string', 'max' => 50],
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
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id']);
    }



}
