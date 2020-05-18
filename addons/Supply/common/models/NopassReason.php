<?php

namespace addons\Style\common\models;
use common\traits\Tree;

/**
 * 款式分类
 * @property int $id 主键
 * @property string $title 标题
 * @property string $tree 树
 * @property int $sort 排序
 * @property int $level 级别
 * @property int $pid 上级id
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class NopassReason extends BaseModel
{
    use Tree;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("nopass_reason");
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['status','name'], 'required'],
                [['id','merchant_id','sort', 'level', 'pid', 'status', 'created_at', 'updated_at'], 'integer'],
                [['name'], 'string', 'max' => 100],
                [['image'], 'string', 'max' => 100],
                [['tree'], 'string', 'max' => 255],
                [['pid','level','name'], 'safe'],

        ];
    }


    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'name' => '标题',
                'image' =>  '图标',
                'sort' => '排序',
                'tree' => '树',
                'level' => '级别',
                'pid' => '父级',
                'status' => '状态',
                'created_at' => '创建时间',
                'updated_at' => '更新时间',
        ];
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'pid']);
    } 
}
