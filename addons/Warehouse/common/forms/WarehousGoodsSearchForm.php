<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;

/**
 * Class ProductSearchForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousGoodsSearchForm extends Model
{
    public $goods_sn;
    public $goods_name;
    public $style_cate_id;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
//            ['recommend', 'safe'],
            [['goods_name','goods_sn'], 'string'],
            [['style_cate_id'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function goods_sn(){
        $where = ['or',['=','goods_id',$this->goods_sn],['=','style_sn',$this->goods_sn],['=','qiban_sn',$this->goods_sn]];
        return $where;
    }
    /**
     * @return array
     */
    public function recommend()
    {
        $where = [];
        if (empty($this->recommend)) {
            return $where;
        }

        foreach ($this->recommend as $value) {
            if ($value == 1) {
                $where['is_hot'] = 1;
            }

            if ($value == 2) {
                $where['is_recommend'] = 1;
            }

            if ($value == 3) {
                $where['is_new'] = 1;
            }



            // 分销
            if ($value == 5) {
                $where['is_open_commission'] = 1;
            }

            // 预售
            if ($value == 6) {
                $where['is_open_presell'] = 1;
            }
        }

        return $where;
    }



    /**
     * @return array
     */
    public function betweenSales()
    {
        if (!empty($this->min_sales) && !empty($this->max_sales)) {
            return ['between', 'total_sales', $this->min_sales, $this->max_sales];
        }

        if (!empty($this->min_sales)) {
            return ['>=', 'total_sales', $this->min_sales];
        }

        if (!empty($this->max_sales)) {
            return ['<=', 'total_sales', $this->max_sales];
        }

        return [];
    }
    /**
     * 分类id
     *
     * @return array
     */
    public function cateIds()
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
}