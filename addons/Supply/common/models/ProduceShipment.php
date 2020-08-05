<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_produce_shipment".
 *
 * @property int $id ID
 * @property int $produce_id 布产单ID
 * @property string $shipment_sn 出货单号
 * @property int $shippent_num 出货数量
 * @property int $failed_num 报废数量
 * @property int $nopass_num 质检未过数量
 * @property string $failed_reason 报废原因
 * @property int $nopass_type 质检未过类型
 * @property int $nopass_reason 未过原因
 * @property int $status 质检结果,1:通过，0：未通过
 * @property string $remark 备注
 * @property int $creator_id 操作人ID
 * @property string $creator 操作人姓名
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class ProduceShipment extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_shipment');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produce_id','shippent_num','nopass_num'], 'required'],
            [['produce_id', 'shippent_num', 'failed_num', 'nopass_num', 'nopass_type', 'nopass_reason', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['shipment_sn'], 'string', 'max' => 20],
            [['shippent_num'],'parseShippentNum'],
            [['nopass_num'],'parseNopassNum'],
            [['failed_reason', 'remark'], 'string', 'max' => 255],
            [['creator'], 'string', 'max' => 30],
        ];
    }

    public function parseShippentNum($attribute, $params){
        if($this->status == 1 && $this->shippent_num < 1){
            $this->addError($attribute, "不能小于1.");
        }elseif($this->status == 0 && $this->shippent_num != 0){
            $this->addError($attribute, "必须是0");
        }
    }
    public function parseNopassNum($attribute, $params){
        if($this->status == 0 && $this->nopass_num < 1){
            $this->addError($attribute, "不能小于1.");
        }elseif($this->status == 1 && $this->nopass_num != 0){
            $this->addError($attribute, "必须是0");
        }

        if($this->status == 0 && empty($this->nopass_reason)){
            $this->addError("nopass_reason","选择未过原因");
        }

    }




    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produce_id' => '布产单ID',
            'shipment_sn' => '出货单号',
            'shippent_num' => '出货数量',
            'failed_num' => '报废数量',
            'nopass_num' => '质检未过数量',
            'failed_reason' => '报废原因',
            'nopass_type' => '质检未过类型',
            'nopass_reason' => '未过原因',
            'status' => '质检结果',
            'remark' => '备注',
            'creator_id' => '操作人ID',
            'creator' => '操作人姓名',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
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
            $this->creator = Yii::$app->user->identity->username;
        }

        return parent::beforeSave($insert);
    }


    /**
     * 对应布产
     * @return \yii\db\ActiveQuery
     */
    public function getProduce()
    {
        return $this->hasOne(Produce::class, ['id'=>'produce_id']);
    }
}
