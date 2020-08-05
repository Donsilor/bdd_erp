<?php

namespace addons\Style\services;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\models\DiamondLog;
use common\components\Service;
use addons\Style\common\models\Diamond;
use addons\Style\common\models\Style;
use addons\Style\common\models\Goods;
use addons\Style\common\models\StyleLang;
use common\enums\AuditStatusEnum;
use common\enums\InputTypeEnum;
use yii\base\Exception;
use yii\db\Expression;


/**
 * Class DiamondService
 * @package services\common
 */
class DiamondService extends Service
{
    /**
     * 更改基本库存
     * @param unknown $goods_id
     * @param unknown $quantity
     * @param unknown $for_sale 是否销售库存
     */
    public function updateGoodsStorageForOrder($goods_id,$quantity)
    {
        $data = [
            'goods_num'=> new Expression("goods_num+({$quantity})"),
            'sale_volume'=> new Expression("sale_volume-({$quantity})")
        ];
        Diamond::updateAll($data,['goods_id'=>$goods_id]);
    }   
    /**
     * 创建款号(style表)
     * @param unknown $diamond_id
     */
    public function syncDiamondToGoods($diamond_id)
    {
        $diamond = Diamond::find()->where(['id'=>$diamond_id])->one();
        $style = Style::find()->where(['style_sn'=>$diamond->goods_sn])->one();
        if(!$style) {
            $style = new Style();
            $style->style_sn = $diamond->goods_sn;            
        }
        $style->type_id = $diamond->type_id;
        $style->style_image = $diamond->goods_image;
        $style->goods_images = $diamond->parame_images;
        $style->style_3ds = $diamond->goods_3ds;
        $style->style_salepolicy = $diamond->sale_policy;
        $style->goods_storage = $diamond->goods_num;
        $style->sale_price = $diamond->sale_price;
        $style->cost_price = $diamond->cost_price;
        $style->market_price = $diamond->market_price;
        $style->sale_volume = $diamond->sale_volume;
        $style->goods_clicks = $diamond->goods_clicks;
        $style->virtual_clicks = $diamond->virtual_clicks;
        $style->status = $diamond->status;
        if(false === $style->save()) {
            throw new Exception($this->getError($style));
        }
        $style_id = $style->id;
        
        foreach ($diamond->langs as $lang){
            $styleLang = StyleLang::find()->where(['master_id'=>$style_id,'language'=>$lang->language])->one();
            if(!$styleLang) {
                $styleLang = new StyleLang();
                $styleLang->master_id= $style_id;
                $styleLang->language= $lang->language;                
            }
            $styleLang->style_name = $lang->goods_name;
            $styleLang->style_desc = $lang->goods_desc;
            $styleLang->goods_body = $lang->goods_body;
            $styleLang->meta_title = $lang->meta_title;
            $styleLang->meta_word = $lang->meta_word;
            $styleLang->meta_desc = $lang->meta_desc;
            $styleLang->save(false);
        }
        
        $goods = Goods::find()->where(['goods_sn'=>$diamond->goods_sn,'style_id'=>$style->id])->one();        
        if(!$goods) {
            $goods = new Goods();
            $goods->goods_sn = $diamond->goods_sn;
            $goods->style_id = $style->id;
        }
        $goods->type_id = $diamond->type_id;
        $goods->goods_image = $diamond->goods_image;
        $goods->goods_storage = $diamond->goods_num;
        $goods->sale_price = $diamond->sale_price;
        $goods->cost_price = $diamond->cost_price;
        $goods->market_price = $diamond->market_price;
        $goods->sale_volume = $diamond->sale_volume;
        $goods->goods_clicks = $diamond->goods_clicks;
        $goods->status = $diamond->status;
        if(false === $goods->save()) {
            throw new Exception($this->getError($goods));
        }
        $goods_id = $goods->id;
        //echo $style->id , $goods->id;exit;
        //更新裸钻
        $diamond->style_id = $style_id;
        $diamond->goods_id = $goods_id;
        $diamond->save(false);
        
        \Yii::$app->services->salepolicy->syncGoodsMarkup($style_id);        
    }


    /**
     * 单据日志
     * @param array $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseBillLog
     */
    public function createLog($log){

        $model = new DiamondLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }


    /**
     * @param $model
     * @return array
     * 裸钻字段映射
     */
    public function getMapping(){

        $attr_list = array(
            //证书类型
            [
                'attr_id' => AttrIdEnum::DIA_CERT_TYPE,
                'attr_field' => 'cert_type',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 1,

            ],
            //证书号
            [
                'attr_id' => AttrIdEnum::DIA_CERT_NO,
                'attr_field' => 'cert_id',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 1,

            ],
            //石重
            [
                'attr_id' => AttrIdEnum::DIA_CARAT,
                'attr_field' => 'carat',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 1

            ],
            //净度
            [
                'attr_id' => AttrIdEnum::DIA_CLARITY,
                'attr_field' => 'clarity',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 1

            ],
            //切工
            [
                'attr_id' => AttrIdEnum::DIA_CUT,
                'attr_field' => 'cut',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 1
            ],
            //颜色
            [
                'attr_id' => AttrIdEnum::DIA_COLOR,
                'attr_field' => 'color',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 1
            ],
            //形状
            [
                'attr_id' => AttrIdEnum::DIA_SHAPE,
                'attr_field' => 'shape',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 1
            ],
            //切割深度
            [
                'attr_id' => AttrIdEnum::DIA_CUT_DEPTH,
                'attr_field' => 'depth_lv',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
            //台宽比
            [
                'attr_id' => AttrIdEnum::DIA_TABLE_LV,
                'attr_field' => 'table_lv',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
            //对称
            [
                'attr_id' => AttrIdEnum::DIA_SYMMETRY,
                'attr_field' => 'symmetry',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0
            ],
            //抛光
            [
                'attr_id' => AttrIdEnum::DIA_POLISH,
                'attr_field' => 'polish',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0
            ],
            //荧光
            [
                'attr_id' => AttrIdEnum::DIA_FLUORESCENCE,
                'attr_field' => 'fluorescence',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0
            ],
            //石底层
            [
                'attr_id' => AttrIdEnum::DIA_STONE_FLOOR,
                'attr_field' => 'stone_floor',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
            //长度
            [
                'attr_id' => AttrIdEnum::DIA_LENGTH,
                'attr_field' => 'length',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
            //宽度
            [
                'attr_id' => AttrIdEnum::DIA_WIDTH,
                'attr_field' => 'width',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
            //长宽比
            [
                'attr_id' => AttrIdEnum::DIA_ASPECT_RATIO,
                'attr_field' => 'aspect_ratio',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
        );
        return $attr_list;
    }

}