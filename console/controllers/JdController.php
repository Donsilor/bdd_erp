<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\Console;
use addons\Sales\common\models\OrderGoods;

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
    public function actionUpdateJdWare($time_val = 1, $time_type = 1, $order_type = 1, $start_time = 0)
    {
        Console::output("Update JD Sku BEGIN[".date('Y-m-d H:i:s')."]-------------------");
        $wareIds = OrderGoods::find()->select(['out_ware_id'])->distinct(true)->where(['<>','out_ware_id',''])->asArray()->all();
        $wareIds = $wareIds ? array_column($wareIds, 'out_ware_id') : [];
        $group_list = $this->groupArray($wareIds,20);
        foreach ($group_list as $wareIds) {
            $wareIds = implode(",", $wareIds);
            try{
                $wareList = \Yii::$app->jdSdk->getWareList($wareIds);
            }catch (\Exception $e) {
                Console::output("error=>".$e->getMessage());
                continue;
            }
            //print_r($wareList);exit;
            foreach ($wareList as $ware) {
                try{
                    \Yii::$app->salesService->jdOrder->syncOrderGoods($ware);
                    Console::output($ware->wareId." Success");
                }catch (\Exception $e) {
                    Console::output("{$ware->wareId}: error=>".$e->getMessage());
                }
            }
        }
        Console::output("Update JD Sku END[".date('Y-m-d H:i:s')."]-------------------");
    }
    /**
     * 数组分组
     * @param unknown $array
     * @param number $group_size
     */
    private function groupArray($array,$group_size = 30)
    {
        if(empty($array) || !is_array($array)) {
            return [];
        }
        $per    = ceil(count($array)/$group_size);
        $group = [];
        foreach ($array as $key=>$vo) {
            $group[$key%$per][] = $vo;
        }
        return $group;
    }
    /**
     * 同步订单
     * @param number $time_val
     * @param number $time_type
     * @param number $order_type
     * @param number $start_time
     */
    public function actionPullJdOrders($time_val = 1, $time_type = 1, $order_type = 1, $start_time = 0)
    {        
        Console::output("Pull JD Orders BEGIN[".date('Y-m-d H:i:s')."]-------------------");       
        $change_type = $time_type == 1 ? "day" : "month";
        $time_format = $time_type == 1 ? "Y-m-d 00:00:00" : "Y-m-d 00:00:00";        
        $start_time = empty($start_time) ? strtotime("-".$time_val." {$change_type}",time()): strtotime($start_time);
        $break = false;
        for ($val = 0; $val < $time_val; $val++) {
            $start_date = date("Y-m-d H:i:s", strtotime("+".$val." {$change_type}", $start_time)); 
            $end_date   = date("Y-m-d H:i:s", strtotime("+".($val+1)." {$change_type}", $start_time));
            if(strtotime($end_date) > time()) {
                $end_date = date("Y-m-d H:i:s");
                $break = true;
            }
            Console::output("Date : [".$start_date.'] TO ['.$end_date.']');
            $this->syncOrderByDate($start_date, $end_date, $order_type);
            if($break) {
                break;
            }
        }
        Console::output("Pull JD Orders END[".date('Y-m-d H:i:s')."]-------------------");
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
     * 同步一组订单
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