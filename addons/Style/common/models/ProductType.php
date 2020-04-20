<?php

namespace addons\Style\common\models;

use Yii;
use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\traits\Tree;

/**
 * This is the model class for table "{{%addon_article_cate}}".
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
class ProductType extends BaseModel
{
    use Tree, MerchantBehavior;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("product_type");
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['name','status'], 'required'],
                [['id','merchant_id','is_combine','sort', 'level', 'pid', 'status', 'created_at', 'updated_at'], 'integer'],
                [['image'], 'string', 'max' => 100],
                [['tree'], 'string', 'max' => 255],
                [['pid','name'], 'safe'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'name' => '产品线',
                'image' =>  '图标',
                'sort' => '排序',
                'tree' => '树',
                'is_combine' => '镶嵌分类',
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
