<?php

namespace addons\Style\services;

use common\components\Service;
use addons\Style\common\models\Style;
use addons\Style\common\models\Goods;
use common\enums\StatusEnum;
use addons\Style\common\models\StyleGoods;


/**
 * Class GoodsService
 * @package services\common
 */
class StyleGoodsService extends Service
{   
    /**
     * 生成款式商品
     * @param unknown $style_id
     * @param unknown $goods_list
     */
    public function createStyleGoods($style_id,$goods_list)
    {
        $style = Style::find()->where(['id'=>$style_id])->one();
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
        StyleGoods::updateAll($goods_update,['style_id'=>$style_id]);
        foreach ($goods_list as $goods) {
            $styleGoods = StyleGoods::find()->where(['style_id'=>$style_id,'spec_key'=>$key])->one();
            if(!$styleGoods) {
                //新增
                $styleGoods = new StyleGoods();
            }
            $styleGoods->attributes = $goods;
            $styleGoods->style_id = $style->id;
            $styleGoods->style_cate_id = $style->style_cate_id;
            $styleGoods->product_type_id = $style->product_type_id;
            $styleGoods->goods_image  = $style->style_image;//商品默认图片
            $styleGoods->status  = $goods['status']? 1: 0;//商品状态
            $res = $styleGoods->save();
            print_r($res);
        }
        
    }   
    
    /**
     * 下单商品库存更改
     * @param unknown $goods_id  商品ID
     * @param unknown $quantity  变化数量
     * @param unknown $for_sale 销售
     */
   /*  public function updateGoodsStorageForOrder($goods_id,$quantity,$goods_type)
    {        
        if($goods_type == \Yii::$app->params['goodsType.diamond']){
            \Yii::$app->services->diamond->updateGoodsStorageForOrder($goods_id, $quantity);
        }else {
            $data = [
                'goods_storage'=> new Expression("goods_storage+({$quantity})"),
                'sale_volume'  =>new Expression("sale_volume-({$quantity})")
            ];            
            Goods::updateAll($data,['id'=>$goods_id]);
            Style::updateAll($data,['in','id',Goods::find()->select(['style_id'])->where(['id'=>$goods_id])]);
        }
    } */

}