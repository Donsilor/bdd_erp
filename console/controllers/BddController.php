<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use addons\Shop\common\models\Order;
use addons\Shop\common\models\OrderSync;
use addons\Shop\common\enums\OrderStatusEnum;
use addons\Shop\common\enums\SyncPlatformEnum;



/**
 * 订单任务处理
 * Class CommendController
 * @package console\controllers
 */
class BddController extends Controller
{
    /**
     * 拉取BDD官网订单
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
                            \Yii::$app->salesService->bddOrder->syncOrder($order->id);
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