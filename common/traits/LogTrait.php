<?php

namespace common\traits;

use Yii;
use common\models\base\BaseModel;
use common\enums\StatusEnum;

/**
 * Trait LogTrait
 * @package common\traits
 * @property \yii\db\ActiveRecord|\yii\base\Model $modelClass
 * @property string $appId 应用id
 * @property bool $sourceAuthChild 权限来源(false:所有权限，true：当前角色)
 * @property string $viewPrefix 加载视图
 * @author jianyan74 <751393839@qq.com>
 */
trait LogTrait
{
    /**
     * 启用/禁用 日志
     * @param BaseModel $model
     */
    public function statusLog($model)
    {
         $id = $model->id;
         $statusName = StatusEnum::getValue($model->status);
    }
    /**
     * 删除 日志
     * @param BaseModel $model
     */
    public function deleteLog($model)
    {
        
    }
    /**
     * 新增 日志
     * @param BaseModel $model
     */
    public function insertLog($model)
    {
        
    }
    /**
     * 更新日志
     * @param BaseModel $newModel
     * @param BaseModel $oldModel
     */
    public function updateLog($newModel,$oldModel)
    {
        
    }
    /**
     * 审核日志
     * @param BaseModel $model
     */
    public function auditLog($model)
    {
        
    }
    /**
     * 
     * @param array $log
     */
    public function createLog($log) 
    {
        throw new \Exception("请重写createLog方法");
    }
    
}