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
        Console::output("Sync JD Order BEGIN[".date('Y-m-d H:i:s')."]-------------------"); 
        try{
            $page = 1;
            list($order_list,$page_count) = \Yii::$app->jdSdk->getOrderList(null,null,$page);  
            $this->syncOrders($order_list);
        }catch (\Exception $e) {
            Console::output("Page[".$page."],Error:".$e->getMessage());
            return;
        }
        for ($page = 2 ; $page < $page_count; $page ++) {
            Console::output("Page[".$page."] Start");
            try{
                list($order_list) = \Yii::$app->jdSdk->getOrderList(null,null,$page);
            }catch (\Exception $e) {
                Console::output("Page[".$page."],Error:".$e->getMessage());
            }                
            $this->syncOrders($order_list);
            Console::output("Page[".$page."] END ");            
        }
        Console::output("Sync JD Order END[".date('Y-m-d H:i:s')."]-------------------"); 
    }
    /**
     * 同步
     * @param unknown $order_list
     */
    private function syncOrders($order_list) {
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