<?php

namespace addons\Style\common\forms;

use Yii;

use addons\Style\common\models\Style;
use yii\base\Model;
use addons\Style\common\models\StyleGoods;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\enums\AttrTypeEnum;
use common\enums\StatusEnum;

/**
 * 款式编辑-商品属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class StyleGoodsForm extends Model
{
    
    //款式ID
    public $style_id;
    //款式分类ID
    public $style_cate_id;
    //款式编号
    public $style_sn;
    //款式规格属性
    public $style_spec;
    //是否镶嵌
    public $is_combine;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['style_id','style_cate_id','style_sn','is_combine'], 'required'],
                [['style_spec'],'safe']                
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [                
                'style_id'=>'款式id',
                'style_sn'=>'款式编号',
                'style_cate_id'=>'款式分类id',
                'style_spec'=>'款式规格',
        ];
    }
    public function getPostAttrs()
    {
        return $this->style_spec['a'] ?? [];
    }
    /**
     * 款式基础属性
     */
    public function getPostGoods()
    {

        $spec_b = $this->style_spec['b'] ??[];
        $spec_c = $this->style_spec['c'] ??[];
        $goods_list = [];
        foreach ($spec_c as $spec_key =>$goods) {
            if(!$spec_b[$spec_key]) {
                continue;
            }
            $attr_ids = explode(",", $spec_b[$spec_key]['ids']);
            $attr_vids = explode(",", $spec_b[$spec_key]['vids']);
            $goods_sn = $this->style_sn;
            foreach ($attr_ids as $k=>$attr_id){
                $attr_value_id = $attr_vids[$k];
                $goods_spec[$attr_id] = $attr_value_id;
                $goods_sn .= '-'.$attr_value_id;
                $attr_value = Yii::$app->attr->valueName($attr_value_id);
                if($attr_id == AttrIdEnum::FINGER) {
                    $goods['finger'] = $attr_value;
                }elseif($attr_id == AttrIdEnum::MATERIAL) {
                    $goods['material'] = $attr_value_id;
                }
            }
            $goods['spec_key'] =  $spec_key.'';
            $goods['goods_spec'] = json_encode($goods_spec,true); 
            $goods['goods_sn'] = $goods_sn;            
            $goods_list[] = $goods;
        }
        return $goods_list;
    }
    /**
     * 自动填充已填写 表单属性
     */
    public function initGoods()
    {
        $goods_list = StyleGoods::find()->where(['style_id'=>$this->style_id])->all();
        if(empty($goods_list)) {
            return ;
        }
        $spec_a = [];//选中属性数组
        $spec_c = [];//商品属性固定填写内容
        foreach ($goods_list as $goods) {
            $goods_spec = json_decode($goods['goods_spec'],true);
            if(!empty($goods_spec)) {
                foreach ($goods_spec as $attr_id=>$attr_value){
                    $spec_a[$attr_id][] = $attr_value;
                }
            }
            $_goods = array();
            $_goods['goods_sn'] = $goods['goods_sn'];
            $_goods['cost_price'] = $goods['cost_price']/1;
            $_goods['gold_price'] = $goods['gold_price']/1;
            $_goods['second_stone_weight1'] = $goods['second_stone_weight1']/1;
            $_goods['second_stone_num1'] = $goods['second_stone_num1']/1;
            $_goods['second_stone_weight2'] = $goods['second_stone_weight2']/1;
            $_goods['second_stone_num2'] = $goods['second_stone_num2']/1;
            $_goods['gold_weight'] = $goods['gold_weight']/1;
            $_goods['gold_weight_diff'] = $goods['gold_weight_diff']/1;            
            $_goods['finger_range'] = $goods['finger_range']/1;
            $_goods['status'] = $goods['status']?1:0;  
            $_goods['remark'] = $goods['remark'];      
            $spec_c[$goods['spec_key']] = $_goods;
        }
        $this->style_spec  = [
                'a'=>$spec_a,
                'c'=>$spec_c                
        ];
    }
    /**
     * 生成款式商品
     * @param unknown $style_id
     * @param unknown $goods_list
     */
    public function createGoods()
    {
        $goods_list = $this->getPostGoods();
        $style = Style::find()->where(['id'=>$this->style_id])->one();
        if(empty($style) || empty($goods_list)) {
            return false;
        }
        //批量更新款式商品
        $goods_update = [
                'style_sn'=>$style->style_sn,
                'goods_name'=>$style->style_name,
                'goods_image'=>$style->style_image,
                'status'=> StatusEnum::DISABLED,
        ];
        StyleGoods::updateAll($goods_update,['style_id'=>$this->style_id]);
        $cost_prices = array();
        $goods_num   = 0;
        foreach ($goods_list as $goods) {
            $model = StyleGoods::find()->where(['style_id'=>$this->style_id,'spec_key'=>$goods['spec_key']])->one();
            if(!$model) {
                //新增
                $model = new StyleGoods();
            }
            $model->attributes = $goods;
            $model->style_id = $style->id;
            $model->style_cate_id = $style->style_cate_id;
            $model->product_type_id = $style->product_type_id;
            $model->goods_image  = $style->style_image;//商品默认图片
            $model->status  = $goods['status']? 1: 0;//商品状态
            if(!$model->save()) {
                throw new \Exception($this->getError($model));
            }
            $cost_prices[] = $model->cost_price;
            $goods_num += $model->status == 1 ? 1 : 0;
        }
        $cost_price_min = min($cost_prices);
        $cost_price_max = max($cost_prices);
        
        $style->goods_num = $goods_num;
        $style->cost_price = $cost_price_min;
        $style->cost_price_min = $cost_price_min;
        $style->cost_price_max = $cost_price_max;
        if(!$style->save(false)) {
            throw new \Exception($this->getError($style));
        }
    } 
    /**
     * 获取销当前款的售属性列表
     */
    public function getSaleAttrList()
    {
        return StyleAttribute::find()->select(['attr_id','attr_values'])->where(['style_id'=>$this->style_id,'attr_type'=>AttrTypeEnum::TYPE_SALE])->asArray()->all();
    }
    /**
     * 获取skuTable 扩展字段配置
     */
    public function getSKuTableInputs()
    {
        $config = [
                'gold_price'=>['name'=>'gold_price','title'=>"金托成本",'require'=>1,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:100px'],
                'gold_weight'=>['name'=>'gold_weight','title'=>'金托重量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                'gold_weight_diff'=>['name'=>'gold_weight_diff','title'=>'金托上下公差','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                'second_stone_weight1'=>['name'=>'second_stone_weight1','title'=>'副石1重量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                'second_stone_num1'=>['name'=>'second_stone_num1','title'=>'副石1数量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:50px'],
                'second_stone_weight2'=>['name'=>'second_stone_weight2','title'=>'副石2重量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                'second_stone_num2'=>['name'=>'second_stone_num2','title'=>'副石2数量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:50px'],
                'finger_range'=>['name'=>'finger_range','title'=>'改圈范围','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:50px'],
                'remark'=>['name'=>'remark','title'=>'备注','require'=>0,'batch'=>0,'unique'=>0,'dtype'=>"string",'style'=>'width:200px'],
        ];
        $inputs =  [
                ['name'=>'status','title'=>'操作','require'=>0,'batch'=>0,'unique'=>0,'dtype'=>"integer"],
                ['name'=>'goods_sn','title'=>"商品编号",'require'=>0,'batch'=>0,'unique'=>0,'dtype'=>"string",'style'=>'width:150px','attrs'=>'disabled placeholder=\'系统自动生成\''],
                ['name'=>'cost_price','title'=>"总成本",'require'=>1,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:100px'],
        ];
        $maps = [
                //女戒-镶嵌
                '2-1'=>['gold_price','gold_weight','gold_weight_diff','finger_range','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //女戒-非镶嵌
                '2-0'=>['gold_price','gold_weight','gold_weight_diff','finger_range','remark'],
                //项链-镶嵌
                '4-1'=>['gold_price','gold_weight','gold_weight_diff','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //项链-非镶嵌
                '4-0'=>['gold_price','gold_weight','gold_weight_diff','remark'],
                //吊坠-镶嵌
                '5-1'=>['gold_price','gold_weight','gold_weight_diff','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //吊坠-非镶嵌
                '5-0'=>['gold_price','gold_weight','gold_weight_diff','remark'],
                //耳钉-镶嵌
                '6-1'=>['gold_price','gold_weight','gold_weight_diff','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //耳钉-非镶嵌
                '6-0'=>['gold_price','gold_weight','gold_weight_diff','remark'],
                //耳环-镶嵌
                '7-1'=>['gold_price','gold_weight','gold_weight_diff','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //耳环-非镶嵌
                '7-0'=>['gold_price','gold_weight','gold_weight_diff','remark'],
                //手链-镶嵌
                '8-1'=>['gold_price','gold_weight','gold_weight_diff','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //手链-非镶嵌
                '8-0'=>['gold_price','gold_weight','gold_weight_diff','remark'],
                //手镯-镶嵌
                '9-1'=>['gold_price','gold_weight','gold_weight_diff','second_stone_weight1','second_stone_num1','second_stone_weight2','second_stone_num2','remark'],
                //手镯-非镶嵌
                '9-0'=>['gold_price','gold_weight','gold_weight_diff','remark'],
                
        ];

        $key = $this->style_cate_id.'-'.$this->is_combine;
        if(isset($maps[$key])){
            foreach ($maps[$key] as $field){
                if(isset($config[$field])) {
                    $inputs[] = $config[$field];
                }
            }
            
        }
        return $inputs;
    }   
    
}
