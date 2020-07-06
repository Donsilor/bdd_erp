<?php

namespace addons\Warehouse\services;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\forms\QibanAttrForm;
use addons\Style\common\models\Qiban;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use common\enums\StatusEnum;
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
     * @param int $bill_id
     * @throws
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
     * @param WarehouseBillTGoodsForm $form
     * @throws
     */
    public function addBillTGoods($form){

        if(!$form->goods_sn){
            throw new \Exception("款号/起版号不能为空");
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
        $goods_num = 1;
        if($form->is_wholesale){//批发
            $goods_num = $form->goods_num;
            $form->goods_num = 1;
        }
        $style  = Style::find()->where(['style_sn'=>$form->goods_sn])->one();
        if(!$style) {
            $qiban = Qiban::find()->where(['qiban_sn'=>$form->goods_sn])->one();
            if(!$qiban) {
                throw new \Exception("[款号/起版号]不存在");
            }elseif($qiban->status != StatusEnum::ENABLED) {
                throw new \Exception("起版号不可用");
            }else{
                $exist = WarehouseBillGoodsL::find()->where(['bill_id'=>$form->bill_id, 'qiban_sn'=>$form->goods_sn, 'status'=>StatusEnum::ENABLED])->count();
                if($exist) {
                    //throw new \Exception("起版号已添加过");
                }
                if($form->cost_price){
                    $qiban->cost_price = $form->cost_price;
                }
                $goods = [
                    'goods_sn'=>$form->goods_sn,
                    'goods_name' =>$qiban->qiban_name,
                    'style_id' => $qiban->id,
                    'style_sn' => $form->goods_sn,
                    'goods_image' => $style->style_image,
                    'qiban_type'=> $qiban->qiban_type,
                    'product_type_id'=>$qiban->product_type_id,
                    'style_cate_id'=>$qiban->style_cate_id,
                    'style_channel_id'=>$qiban->style_channel_id,
                    'style_sex' => $qiban->style_sex,
                    'goods_num' => $goods_num,
                    'jintuo_type' => $qiban->jintuo_type,
                    'cost_price' => bcmul($qiban->cost_price,$goods_num,3),
                    //'market_price' => $style->market_price,
                    'remark' => $qiban->remark,
                    'creator_id' => \Yii::$app->user->identity->getId(),
                    'created_at' => time(),
                ];
            }
        }elseif($style->status != StatusEnum::ENABLED) {
            throw new \Exception("款号不可用");
        }else{
            if($form->cost_price){
                $style->cost_price = $form->cost_price;
            }
            $goods = [
                'goods_sn'=>$form->goods_sn,
                'goods_name' =>$style->style_name,
                'style_id' => $style->id,
                'style_sn' => $form->goods_sn,
                'goods_image' => $style->style_image,
                'qiban_type'=>QibanTypeEnum::NON_VERSION,
                'product_type_id'=>$style->product_type_id,
                'style_cate_id'=>$style->style_cate_id,
                'style_channel_id'=>$style->style_channel_id,
                'style_sex' => $style->style_sex,
                'goods_num' => $goods_num,
                'jintuo_type' => JintuoTypeEnum::Chengpin,
                'cost_price' => bcmul($style->cost_price,$goods_num,3),
                //'market_price' => $style->market_price,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
        }
        $bill = WarehouseBill::findOne(['id'=>$form->bill_id]);
        $goodsM = new WarehouseBillGoodsL();
        $goodsInfo = [];
        for ($i=0; $i<$form->goods_num; $i++){
            $goodsInfo[$i]= $goods;
            $goodsInfo[$i]['bill_id'] = $form->bill_id;
            $goodsInfo[$i]['bill_no'] = $bill->bill_no;
            $goodsInfo[$i]['bill_type'] = $bill->bill_type;
            $goodsInfo[$i]['goods_id'] = SnHelper::createGoodsId();
            $goodsInfo[$i]['is_wholesale'] = $form->is_wholesale;//批发
            $goodsM->setAttributes($goodsInfo[$i]);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goodsInfo[0]);
        foreach ($goodsInfo as $item) {
            $value[] = array_values($item);
            if(count($value)>=10){
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
                if(false === $res){
                    throw new \Exception("创建收货单据明细失败1");
                }
                $value=[];
            }
        }
        if(!empty($value)){
            $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
            if(false === $res){
                throw new \Exception("创建收货单据明细失败2");
            }
        }

        $this->warehouseBillTSummary($form->bill_id);
    }

}