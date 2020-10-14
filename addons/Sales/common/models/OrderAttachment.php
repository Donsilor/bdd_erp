<?php

namespace addons\Sales\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "sales_order_attachment".
 *
 * @property int $id 主键
 * @property int $order_id 订单id
 * @property string $file 文件
 */
class OrderAttachment extends \addons\Sales\common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_attachment');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id','creator_id','created_at','updated_at'], 'integer'],
            [['file'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'file' => '文件',
            'creator_id' => '添加人',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
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
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
}
