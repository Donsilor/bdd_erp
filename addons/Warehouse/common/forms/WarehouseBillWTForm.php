<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBillW;
use common\enums\StatusEnum;
/**
 * 盘点  Form
 *
 */
class WarehouseBillWForm extends WarehouseBill
{
    public $goods_ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['to_warehouse_id'], 'required'],
                ['goods_ids','string'],
                [['to_warehouse_id'],'checkWarehouse']
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    { 
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'goods_ids'=>'货号',
                'to_warehouse_id'=>'盘点仓库'
        ]);
    }
    /**
     * 字符串转换成数组
     */
    public function getGoodsIds()
    {
        if($this->goods_ids == '') {
            throw new \Exception("货号不能为空");
        }
        return StringHelper::explodeIds($this->goods_ids);
    }
    
    /**
     * 检查仓库是否可以盘点
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function checkWarehouse($attribute)
    {   
        //仅新增时验证
        if(!$this->id) {        
            $model = Warehouse::find()->select(['id','status'])->where(['id'=>$this->to_warehouse_id])->one();
            if(!$model) {
                $this->addError($attribute,'仓库不存在');
            }else if($model->status != StatusEnum::ENABLED){
                $this->addError($attribute,"仓库已被".StatusEnum::getValue($model->status));
            }
        }
    }
    /**
     * 获取仓库下拉列表
     * @return unknown
     */
    public function getWarehouseDropdown()
    {
        if($this->id) {
            return \Yii::$app->warehouseService->warehouse->getDropDown();
        }else{
            return \Yii::$app->warehouseService->warehouse->getDropDownForUnlock();
        }
    }
    
}
