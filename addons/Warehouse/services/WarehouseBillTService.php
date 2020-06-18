<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\models\Style;
use addons\Warehouse\common\models\WarehouseBillGoodsL;

/**
 * 其他收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillTService extends Service
{

    /**
     * 单据汇总
     * @param unknown $bill_id
     */
    public function warehouseBillTSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoodsL::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(market_price) as total_market'])
            ->where(['bill_id'=>$bill_id])
            ->asArray()->one();
        if($sum) {
            $result = WarehouseBill::updateAll(['goods_num'=>$sum['goods_num']/1, 'total_cost'=>$sum['total_cost']/1, 'total_market'=>$sum['total_market']/1],['id'=>$bill_id]);
        }
        return $result;
    }

    /**
     * 添加明细
     * @param array $goods
     * @param array $bill
     * @param array $bill_goods
     */
    public function addBillTGoods($form){

        if(!$form->style_sn){
            throw new \Exception("款号不能为空");
        }
        if(!$form->goods_num){
            throw new \Exception("商品数量必填");
        }
        if(!is_numeric($form->goods_num)){
            throw new \Exception("商品数量不合法");
        }
        if($form->goods_num <= 0){
            throw new \Exception("商品数量必须大于0");
        }
        if($form->goods_num > 100){
            throw new \Exception("一次最多只能添加100个商品，可分多次添加");
        }
        $style = Style::findOne(['style_sn'=>$form->style_sn]);
        if(!$style){
            throw new \Exception("款号不存在");
        }
        $bill = WarehouseBill::findOne(['id'=>$form->bill_id]);
        $goodsM = new WarehouseBillGoodsL();
        $goods = [
            'goods_name' =>$style->style_name,
            'style_sn' => $form->style_sn,
            'product_type_id'=>$style->product_type_id,
            'style_cate_id'=>$style->style_cate_id,
            'style_sex' => $style->style_sex,
            'goods_num' => 1,
            'jintuo_type' => JintuoTypeEnum::Chengpin,
            'cost_price' => $style->cost_price,
            //'market_price' => $style->market_price,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        $goodsInfo = [];
        for ($i=0; $i<$form->goods_num; $i++){
            $goodsInfo[$i]= $goods;
            $goodsInfo[$i]['bill_id'] = $form->bill_id;
            $goodsInfo[$i]['bill_no'] = $bill->bill_no;
            $goodsInfo[$i]['bill_type'] = $bill->bill_type;
            $goodsInfo[$i]['goods_id'] = SnHelper::createGoodsId();
            $goodsM->setAttributes($goodsInfo[$i]);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goodsInfo[0]);
        foreach ($goodsInfo as $item) {
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单据明细失败");
        }

        $this->warehouseBillTSummary($form->bill_id);
    }

}