<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\models\Warehouse;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use Swoole\Http\Status;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseService extends Service
{

    /**
     * 非禁用仓库列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDown()
    {
        $models = Warehouse::find()
            ->where(['>', 'status', StatusEnum::DISABLED])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($models,'id','name');
    } 
    /**
     * 非删除的仓库列表
     * @param unknown $status
     * @return array
     */
    public static function getDropDownForAll()
    {
        $models = Warehouse::find()
            ->where(['>', 'status', StatusEnum::DELETE])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($models,'id','name');
    }
    /**
     * 未锁定可用仓库
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDropDownForUnlock()
    {
        $models = Warehouse::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->select(['id', 'name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        return ArrayHelper::map($models,'id','name');
    }   
    /**
     * 锁定仓库
     * @param int $id
     * @param array $log
     * @return number
     */
    public function lockWarehouse($id, $log = [])
    {  
        return Warehouse::updateAll(['status'=>StatusEnum::LOCKED],['id'=>$id,'status'=>StatusEnum::ENABLED]);
    }
    /**
     * 解锁仓库
     * @param int $id
     * @param array $log
     * @return number
     */
    public function unlockWarehouse($id, $log = [])
    {
        return Warehouse::updateAll(['status'=>StatusEnum::ENABLED],['id'=>$id,'status'=>StatusEnum::LOCKED]);
    }

}