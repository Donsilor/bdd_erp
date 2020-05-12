<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * This is the model class for table "supply_produce_oqc".
 *
 * @property int $id
 * @property int $produce_id 布产ID
 * @property int $pass_num 质检通过数量
 * @property int $nopass_num 质检未过数量
 * @property int $nopass_reason OQC未过原因
 * @property int $pass_result OQC结果
 * @property int $failed_num 质检报废数量
 * @property string $failed_reason 报废原因
 * @property string $remark 操作备注
 * @property int $creator_id 操作人ID
 * @property string $creator 操作人
 * @property int $created_at 操作时间
 * @property int $updated_at
 */
class ProduceOqc extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_oqc');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produce_id', 'pass_result', 'remark', 'creator_id', 'creator'], 'required'],
            [['produce_id', 'pass_num', 'nopass_num', 'nopass_reason', 'pass_result', 'failed_num', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['failed_reason'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 50],
            [['creator'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produce_id' => '布产ID',
            'pass_num' => '质检通过数量',
            'nopass_num' => '质检未过数量',
            'nopass_reason' => 'OQC未过原因',
            'pass_result' => 'OQC结果',
            'failed_num' => '质检报废数量',
            'failed_reason' => '报废原因',
            'remark' => '操作备注',
            'creator_id' => '操作人ID',
            'creator' => '操作人',
            'created_at' => '操作时间',
            'updated_at' => 'Updated At',
        ];
    }
}
