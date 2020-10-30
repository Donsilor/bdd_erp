<?php

namespace common\models\common;

use Yii;
use common\models\base\BaseModel;

/**
 * This is the model class for table "common_quick_config".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户ID
 * @property string $code 编码
 * @property string $name 业务名称
 * @property string $url 跳转URL
 * @property string $image 图片URL
 * @property int $sort 排序
 * @property int $level 级别
 * @property int $pid 上级id
 * @property string $tree 树
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class QuickConfig extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('common_quick_config');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'level', 'pid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'code'], 'string', 'max' => 100],
            [['url', 'image', 'tree'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '商户ID',
            'code' => '权限/编码',
            'name' => '名称',
            'url' => '路由',
            'image' => '图片URL',
            'sort' => '排序',
            'level' => '级别',
            'pid' => '上级id',
            'tree' => '树',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
