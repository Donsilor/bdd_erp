<?php

namespace services\common;
use common\components\Service;
use common\models\common\Flow;


/**
 * Class FlowTypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class FlowService extends Service
{

    public function getFlows($flow_ids){
        return Flow::find()->where(['id'=>$flow_ids])->asArray()->all();
    }







}