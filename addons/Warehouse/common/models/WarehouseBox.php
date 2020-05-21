<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "warehouse_box".
 *
 * @property int $id
 * @property string $box_sn 柜位号
 * @property int $warehouse_id 柜位所属仓库
 * @property string $remark 备注
 * @property int $status 状态(1启用，0禁用 -删除)
 * @property int $creator_id 创建人
 * @property int $updated_at 更新时间
 * @property int $created_at 创建时间
 */
class WarehouseBox extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_box');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['box_sn', 'warehouse_id'], 'required'],
            [['warehouse_id', 'status', 'creator_id', 'updated_at', 'created_at'], 'integer'],
            [['box_sn'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
            [['warehouse_id','box_sn'], 'unique','targetAttribute'=>['warehouse_id','box_sn']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'box_sn' => '柜位号',
            'warehouse_id' => '所属仓库',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
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
     * 关联仓库一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id']);
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
