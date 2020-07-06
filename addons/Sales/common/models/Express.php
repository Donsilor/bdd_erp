<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_express".
 *
 * @property int $id
 * @property string $code 快递编码
 * @property string $cover 快递公司logo
 * @property string $name 快递名称
 * @property int $status 状态 1启用 0禁用
 * @property int $sort
 * @property int $creator_id 创建人
 * @property int $created_at
 * @property int $updated_at
 */
class Express extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('express');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 25],
            [['cover'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '快递编码',
            'cover' => '快递公司logo',
            'name' => '快递名称',
            'status' => '状态',
            'sort' => '排序',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
