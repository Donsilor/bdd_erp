<?php

namespace addons\style\common\models;

use Yii;

use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\traits\Tree;

/**

 *
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
class StyleCate extends BaseModel
{
    use Tree;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::dbName().'.{{style_category}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['status','cate_name'], 'required'],
                [['id','merchant_id','sort', 'level', 'pid', 'status', 'created_at', 'updated_at'], 'integer'],
                [['cate_name'], 'string', 'max' => 100],
                [['image'], 'string', 'max' => 100],
                [['tree'], 'string', 'max' => 255],
                [['pid','cate_name'], 'safe'],

        ];
    }


    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'cate_name' => '分类名称',
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
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        $this->pid = $this->pid ? $this->pid : 0;
        return parent::beforeSave($insert);
    }
    

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'pid']);
    } 
}
