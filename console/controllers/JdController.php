<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;

/**
 * 京东API任务处理
 * Class CommendController
 * @package console\controllers
 */
class JdController extends Controller
{
    /**
     * 同步订单
     * @param number $time_val
     * @param number $time_type
     * @param number $order_type
     * @param number $start_time
     */
    public function actionPullOrders($time_val = 1, $time_type = 1, $order_type = 1, $start_time = 0)
    {
        Console::output("Sync JD Order BEGIN[".date('Y-m-d H:i:s')."]-------------------");
        $change_type = $time_type == 1 ? "day" : "hour";
        $time_format = $time_type == 1 ? "Y-m-d 00:00:00" : "Y-m-d H:00:00";
        $start_time = $start_time == 0 ? time() : strtotime(date('Y-m-d H:i:s', strtotime($start_time)));
        for ($val = $time_val-1; $val >= 0; $val--) {
            $start_date = date("Y-m-d 00:00:00", strtotime("-{$val} {$change_type}", $start_time));   
            $end_date   = date("Y-m-d 23:59:59", strtotime("-{$val} {$change_type}", $start_time));    
            Console::output("Date : [".$start_date.'] TO ['.$end_date.']');
            $this->syncOrderByDate($start_date, $end_date, $order_type);
        }
        Console::output("Sync JD Order END[".date('Y-m-d H:i:s')."]-------------------");
    }
    /**
     * 根据时间同步订单
     * @param unknown $start_date
     * @param unknown $end_date
     */
    private function syncOrderByDate($start_date, $end_date, $order_type)
    {
        try{
            $page = 1;
            list($order_list,$page_count) = \Yii::$app->jdSdk->getOrderList($start_date,$end_date,$page,$order_type);
            $this->syncOrders($order_list);
        }catch (\Exception $e) {
            Console::output("Page[".$page."],Error:".$e->getMessage());
            return;
        }
        for ($page = 2 ; $page <= $page_count; $page ++) {
            Console::output("Page[".$page."] Start");
            try{
                list($order_list) = \Yii::$app->jdSdk->getOrderList($start_date,$end_date,$page,$order_type);
            }catch (\Exception $e) {
                Console::output("Page[".$page."],Error:".$e->getMessage());
            }
            $this->syncOrders($order_list);
            Console::output("Page[".$page."] END ");
        }
    }
    /**
     * 同步
     * @param unknown $order_list
     */
    private function syncOrders($order_list) {
        foreach ($order_list as $order) {
            try{
                \Yii::$app->salesService->jdOrder->syncOrder($order);
                Console::output($order->orderId." Success");
            }catch (\Exception $e) {
                Console::output($order->orderId." Error:".$e->getMessage());
            }
        }
    }    
}