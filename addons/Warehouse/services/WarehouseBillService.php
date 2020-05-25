<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\models\WarehouseBillLog;
use common\helpers\Url;
use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillService extends Service
{

    /**
     * 仓储单据明细 tab
     * @param int $bill_id 单据ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($bill_id, $bill_type, $returnUrl = null)
    {

        switch ($bill_type){
            
            case BillTypeEnum::BILL_TYPE_M:
                {
                    $tab = [
                            1=>['name'=>'单据详情','url'=>Url::to(['warehouse-bill-m/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['warehouse-bill-m-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'日志信息','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                    ];
                    break;
                }               
            case BillTypeEnum::BILL_TYPE_W:
                {
                    $tab = [
                            1=>['name'=>'单据详情','url'=>Url::to(['warehouse-bill-w/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                            2=>['name'=>'单据明细','url'=>Url::to(['warehouse-bill-w-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                            3=>['name'=>'日志信息','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])]
                    ];
                    break;
                }            
            case BillTypeEnum::BILL_TYPE_L:
                {
                    $tab = [
                        1=>['name'=>'单据详情','url'=>Url::to(['warehouse-bill-l/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'单据明细','url'=>Url::to(['warehouse-bill-l-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                        3=>['name'=>'结算商信息','url'=>Url::to(['warehouse-bill-pay/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                        4=>['name'=>'日志信息','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>4,'returnUrl'=>$returnUrl])],
                    ];
                    break;
                }
            case BillTypeEnum::BILL_TYPE_B :
                {
                    $tab = [
                        1=>['name'=>'单据详情','url'=>Url::to(['warehouse-bill-b/view','id'=>$bill_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'单据明细','url'=>Url::to(['warehouse-bill-b-goods/index','bill_id'=>$bill_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                        3=>['name'=>'日志信息','url'=>Url::to(['warehouse-bill-log/index','bill_id'=>$bill_id,'tab'=>3,'returnUrl'=>$returnUrl])],
                    ];
                    break;
                }
            
        }
        return $tab;
    }


    /**
     * 仓储单据汇总
     * @param unknown $bill_id
     */
    public function warehouseBillSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoods::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(sale_price) as total_sale', 'sum(market_price) as total_market'])
            ->where(['bill_id'=>$bill_id, 'status'=>StatusEnum::ENABLED])
            ->asArray()->one();
        if($sum) {
            $result = WarehouseBill::updateAll(['goods_num'=>$sum['goods_num']/1, 'total_cost'=>$sum['total_cost']/1, 'total_sale'=>$sum['total_sale']/1, 'total_market'=>$sum['total_market']/1],['id'=>$bill_id]);
        }
        return $result;
    }


    /**
     * 单据日志
     * @param array $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseBillLog
     */
    public function createWarehouseBillLog($log){
        
        $model = new WarehouseBillLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }


    //统计单据明细数量
    public function sumGoodsNum($bill_id){
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('goods_num');
    }


    //统计单据明细成本价
    public function sumCostPrice($bill_id){
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('cost_price');
    }


    //统计单据明细销售价
    public function sumSalePrice($bill_id){
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('sale_price');
    }


    //统计单据明细市场价
    public function sumMarketPrice($bill_id){
        return WarehouseBillGoods::find()->where(['bill_id' => $bill_id])->sum('market_price');
    }

}