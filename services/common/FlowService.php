<?php

namespace services\common;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\FlowStatusEnum;
use common\models\common\Flow;
use common\models\common\FlowDetails;


/**
 * Class FlowTypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class FlowService extends Service
{

    /***
     * @param $flow_ids
     * @return array|\yii\db\ActiveRecord[]
     * 获取有效的审批流程
     */
    public function getFlows($flow_ids){
        return Flow::find()->where(['id'=>$flow_ids])->andWhere(['<>','flow_status',FlowStatusEnum::CANCEL])->asArray()->all();
    }

    /***
     * @param $flow_type_id
     * @param $target_id
     * @return array
     * 获取单据所有审批流程明细
     */
    public function getFlowDetalsAll($flow_type_id,$target_id){
        $flow_list = Flow::find()->where(['flow_type'=>$flow_type_id,'target_id' => $target_id])->orderBy('id desc')->all();
        $flow_detail_arr = [];
        foreach ($flow_list as $flow){
            $flow_detail = FlowDetails::find()->where(['flow_id'=>$flow->id])->andWhere(['<>','audit_status',AuditStatusEnum::SAVE])->orderBy('id desc')->all();
            $flow_detail_arr = array_merge($flow_detail_arr,$flow_detail);
        }
        return $flow_detail_arr;

    }





}