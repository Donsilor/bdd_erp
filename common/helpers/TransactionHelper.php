<?php

namespace common\helpers;

use Yii;

/**
 * 事务 助手
 * Class TransactionHelper
 * @package common\helpers
 * @author gaopeng
 */
class TransactionHelper 
{
    /**
     * 批量开启事务
     * @param array $dblinks
     */
    public static function beginTransaction(array & $dblinks)
    {
        print_r($dblinks);exit;
        try{
            foreach ($dblinks as $k=>$link) {
                $link->beginTransaction();
                $dblinks[$k] = $link;
            }
        }catch(\yii\db\Exception $e) {
            throw $e;
        }
    }
    /**
     * 批量提交事务
     * @param array $dblinks
     */
    public static function commit(array & $dblinks)
    {
        try{
            foreach ($dblinks as $k=>$link) {
                $link->commit();
                $dblinks[$k] = $link;
            }
        }catch(\yii\db\Exception $e) {
            self::rollback($dblinks);
            throw $e;
        }
    }
    /**
     * 批量回滚事务
     * @param array $dblinks
     */
    public static function rollback(array & $dblinks)
    {
        try{
            foreach ($dblinks as $k=>$link) {
                $link->rollback();
                $dblinks[$k] = $link;
            }
        }catch(\yii\db\Exception $e) {            
            throw $e;
        }
        
    }
}