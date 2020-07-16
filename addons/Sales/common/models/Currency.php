<?php

namespace addons\Sales\common\models;

/**
 * 货币
 *
 * @property int $id ID
 * @property string $name 名称
 * @property string $code 代号
 * @property string $sign 货币符号
 * @property double $rate 设置汇率
 * @property double $refer_rate 参考汇率
 * @property string $status 状态 1启用 0禁用 -1删除
 * @property string $created_at 添加时间
 * @property string $updated_at 更新时间
 */
class Currency extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('currency');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id' , 'name' , 'code' , 'sign' , 'rate' , 'refer_rate' , 'status' , 'created_at' , 'updated_at'], 'trim'],
            [['name' , 'code' , 'sign' , 'rate' , 'refer_rate'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['code'], 'string', 'max' => 5],
            [['sign'], 'string', 'max' => 10],
            [['name' , 'code' , 'sign'], 'safe'],
            [['rate' , 'refer_rate'], 'double'],
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'code' => '代号',
            'sign' => '货币符号',
            'rate' => '设置汇率',
            'refer_rate' => '参考汇率',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }
}