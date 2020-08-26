<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use addons\Shop\common\models\Order;
use addons\Shop\common\models\OrderSync;
use common\enums\ConfirmEnum;
use addons\Shop\common\enums\OrderStatusEnum;
use addons\Shop\common\enums\SyncPlatformEnum;
use ACES\TDEClient;


/**
 * 订单任务处理
 * Class CommendController
 * @package console\controllers
 */
class OrderController extends Controller
{
 
    public function actionTest()
    {
        Console::output("Begin Sync JD Order[".date('Y-m-d H:i:s')."]-------------------"); 
        try{
            list($order_list,$page,$page_count,$order_count) = \Yii::$app->jdSdk->getOrderList(null,null,1);        
        }catch (\Exception $e) {
            Console::output("Page[".$page."],Error:".$e->getMessage());
        }
        for ($page; $page < $page_count; $page ++) {
            try{
                list($order_list,$page,$page_count,$order_count) = \Yii::$app->jdSdk->getOrderList(null,null,++$page);
            }catch (\Exception $e) {
                Console::output("Page[".$page."],Error:".$e->getMessage());
            }                
            foreach ($order_list as $order) {
                try{
                    $trans = \Yii::$app->trans->beginTransaction();
                    \Yii::$app->salesService->jdOrder->syncOrder($order);                    
                    $trans->commit();
                    Console::output($order->orderId." Success");
                }catch (\Exception $e) {
                    Console::output($order->orderId." Error:".$e->getMessage());
                }  
            }
            
        }
        Console::output("End Sync JD Order[".date('Y-m-d H:i:s')."]-------------------"); 
    }
    /**
     * 拉去官网订单
     * @param string $batch
     */
    public function actionPullBddOrders()
    {
        $date = date('Y-m-d H:i:s');
        Console::output("Sync Start[{$date}]-------------------");        
        try {            
            for($page = 1 ; $page <= 100; $page ++) {                
                $order_list = Order::find()->alias('order')
                    ->select(['order.id','order.order_sn'])
                    ->innerJoin(OrderSync::tableName().' sync','order.id=sync.order_id and sync.sync_platform='.SyncPlatformEnum::SYNC_EPR)
                    ->where(['order.is_test'=>0,'sync.sync_created'=>0])
                    ->andWhere(['>=','order.order_status',OrderStatusEnum::ORDER_PAID])
                    ->andWhere(['<','sync.sync_created_time',time()-60])                    
                    ->orderBy('order.id asc')
                    ->limit(50)
                    ->all();
                if(empty($order_list)) {
                    break;
                }
                Console::output("Page[{$page}] Start-------------------");
                foreach ($order_list as $order){
                    $key = "PullBddOrders:{$order->id}";
                    \Yii::$app->cache->getOrSet($key, function () use($order) {                        
                        try {
                            \Yii::$app->shopService->orderSync->syncOrder($order->id);
                            Console::output('success:'.$order->order_sn);
                        } catch (\Exception $exception) {
                            OrderSync::updateAll(['sync_created'=>0,'sync_created_time'=>time()],['order_id'=>$order->id,'sync_platform'=>SyncPlatformEnum::SYNC_EPR]);
                            Console::output('fail:'.$order->order_sn.' , '.$exception->getMessage());
                        }                
                   },60);
                }
                Console::output("Page[{$page}] END-------------------");
            }
        }catch (\Exception $e) {
            //\Yii::$app->services->actionLog->sendNoticeSms('同步BDD订单',"order/pull-bdd-orders",[],3600);
            throw $e;
        }
        Console::output('Sync End----------------------------------------------------');
    }
}