<?php

namespace common\components;

use Yii;
/**
 * Class Debris
 * @package common\components
 * @author jianyan74 <751393839@qq.com>
 */
class Transaction
{
    //事务连接
    public $links;
    //数据库列表
    public $dbs;
        
    /**
     * 批量开启事务
     * @param array $dblinks
     */
    public function beginTransaction(array $dbs = null,$isolationLevel = null)
    {        
        try{     
            $this->unique($dbs);
            foreach ($this->dbs as $k=> $db) {
                $this->links[$k] = $db->beginTransaction($isolationLevel);
            }
        }catch(\Exception $e) {
            throw $e;
        }
        return $this;
    }
    /**
     * 批量提交事务
     * @param array $dblinks
     */
    public function commit()
    {
        try{
            foreach ($this->links as $k=> & $link) {
                $link->commit();
            }
        }catch(\yii\db\Exception $e) {
            self::rollback();
            throw $e;
        }
    }
    /**
     * 批量回滚事务
     * @param array $dblinks
     */
    public function rollback()
    {
        try{
            foreach ($this->links as $k=> & $link) {
                $link->rollback();
            }
        }catch(\yii\db\Exception $e) {
            throw $e;
        }        
    }
    /**
     * 过滤重复数据库连接
     * @param array $dbs
     * @return \common\components\Transaction
     */
    private function unique(array $dbs = null)
    {
        if(empty($dbs)) {
            $dbs = [\Yii::$app->db];
        }
        
        $this->dbs = [];
        foreach ($dbs as $db) {
            preg_match("/host=([^;]+);/i", $db->dsn, $matches);
            $key = md5($matches[1]);
            if(!array_key_exists($key,$this->dbs)) {
                $this->dbs[$key] = $db;
            }
        }
        return $this;
    }
}