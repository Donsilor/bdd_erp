<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use Yii;
use yii\base\Model;

/**
 * Class ProductSearchForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousGoodsSearchForm extends Model
{
    public $goods_id;
    public $style_sn;
    public $qiban_sn;
    public $goods_name;
    public $style_cate_id;
    public $product_type_id;
    public $goods_status;
    public $material_type;
    public $jintuo_type;
    public $main_stone_type;
    public $min_gold_weight;
    public $max_gold_weight;
    public $min_suttle_weight;
    public $max_suttle_weight;
    public $min_diamond_carat;
    public $max_diamond_carat;
    public $warehouse_id;
    public $supplier_id;
    public $style_channel_id;
    public $goods_source;
    public $finger_hk;
    public $finger;
    public $diamond_color;
    public $diamond_clarity;
    public $created_at;
    public $chuku_time;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
//            ['recommend', 'safe'],
            [['goods_name','goods_id','style_sn','created_at','chuku_time'], 'string'],
            [['style_cate_id','product_type_id','goods_status','material_type','jintuo_type','main_stone_type'
                ,'warehouse_id','supplier_id','style_channel_id','goods_source','finger_hk','finger',
                'diamond_color','diamond_clarity'], 'integer'],
            [['min_suttle_weight','max_suttle_weight','min_gold_weight','max_gold_weight','min_diamond_carat',
                'max_diamond_carat'],'number'],
        ];
    }

    /**
     * @return array
     */
    public function goods_sn(){
        $where = ['or',['=','style_sn',trim($this->goods_sn)],['=','qiban_sn',trim($this->goods_sn)]];
        return $where;
    }

    /**
     * @return array
     */
    public function qiban_sn(){
        return trim($this->qiban_sn);
    }
    /**
     * @return array
     */
    public function goods_name(){
        return trim($this->goods_name);
    }
    /**
     * @return array
     */
    public function goods_ids(){
        $goods_arr = StringHelper::explodeIds($this->goods_id);
        return $goods_arr;
    }

    /**
     * @return array
     * 金重
     */
    public function betweenSuttleWeight()
    {
        if (!empty($this->min_suttle_weight) && !empty($this->max_suttle_weight)) {
            return ['between', 'suttle_weight', $this->min_suttle_weight, $this->max_suttle_weight];
        }

        if (!empty($this->min_suttle_weight)) {
            return ['>=', 'suttle_weight', $this->min_suttle_weight];
        }

        if (!empty($this->max_suttle_weight)) {
            return ['<=', 'suttle_weight', $this->max_suttle_weight];
        }

        return [];
    }

    /**
     * @return array
     * 连石重
     */
    public function betweenDiamondCarat()
    {
        if (!empty($this->min_diamond_carat) && !empty($this->max_diamond_carat)) {
            return ['between', 'diamond_carat', $this->min_diamond_carat, $this->max_diamond_carat];
        }

        if (!empty($this->min_diamond_carat)) {
            return ['>=', 'diamond_carat', $this->min_diamond_carat];
        }

        if (!empty($this->max_diamond_carat)) {
            return ['<=', 'diamond_carat', $this->max_diamond_carat];
        }

        return [];
    }

    /**
     * @return array
     * 主石重
     */
    public function betweenGoldWeight()
    {
        if (!empty($this->min_gold_weight) && !empty($this->max_gold_weight)) {
            return ['between', 'gold_weight', $this->min_gold_weight, $this->max_gold_weight];
        }

        if (!empty($this->min_gold_weight)) {
            return ['>=', 'gold_weight', $this->min_gold_weight];
        }

        if (!empty($this->max_gold_weight)) {
            return ['<=', 'gold_weight', $this->max_gold_weight];
        }

        return [];
    }
    /**
     * 分类id
     *
     * @return array
     */
    public function styleCateIds()
    {
        $style_cate_id_arr = [];
        $style_cate_id = $this->style_cate_id;
        if(is_array($style_cate_id)){
            foreach ($style_cate_id as $cate_id){
                $style_cate_id_arr = ArrayHelper::merge($style_cate_id_arr,Yii::$app->styleService->styleCate->findChildIdsById($cate_id));
            }
        }else{
            $style_cate_id_arr = ArrayHelper::merge($style_cate_id_arr,Yii::$app->styleService->styleCate->findChildIdsById($style_cate_id));
        }
        return $style_cate_id_arr;
    }


    /**
     * 产品id
     *
     * @return array
     */
    public function proTypeIds()
    {
        $product_type_id_arr = [];
        $product_type_id = $this->product_type_id;
        if(is_array($product_type_id)){
            foreach ($product_type_id as $pro_type_id){
                $product_type_id_arr = ArrayHelper::merge($product_type_id_arr,Yii::$app->styleService->productType->findChildIdsById($pro_type_id));
            }
        }else{
            $product_type_id_arr = ArrayHelper::merge($product_type_id_arr,Yii::$app->styleService->productType->findChildIdsById($product_type_id));
        }
        return $product_type_id_arr;
    }

    /**
     * 入库时间
     *
     * @return array
     */
    public function betweenCreatedAt()
    {
        if ($this->created_at
            && count($created_ats = explode('/', $this->created_at)) == 2) {
            $created_at_start = strtotime($created_ats[0]);
            $created_at_end = strtotime($created_ats[1]) + 86400;
            if (!empty($created_at_start) && !empty($created_at_end)) {
                return ['between', WarehouseGoods::tableName() . '.created_at', $created_at_start, $created_at_end];
            }
            if (!empty($created_at_start)) {
                return ['>=', WarehouseGoods::tableName() . '.created_at', $created_at_start];
            }
            if (!empty($created_at_end)) {
                return ['<=', WarehouseGoods::tableName() . '.created_at', $created_at_end];
            }
        };
        return [];
    }

    /**
     * 出库时间
     *
     * @return array
     */
    public function betweenChukuTime()
    {
        if ($this->chuku_time
            && count($created_ats = explode('/', $this->chuku_time)) == 2) {
            $created_at_start = strtotime($created_ats[0]);
            $created_at_end = strtotime($created_ats[1]) + 86400;
            if (!empty($created_at_start) && !empty($created_at_end)) {
                return ['between', WarehouseGoods::tableName() . '.chuku_time', $created_at_start, $created_at_end];
            }
            if (!empty($created_at_start)) {
                return ['>=', WarehouseGoods::tableName() . '.chuku_time', $created_at_start];
            }
            if (!empty($created_at_end)) {
                return ['<=', WarehouseGoods::tableName() . '.chuku_time', $created_at_end];
            }
        };
        return [];
    }
}