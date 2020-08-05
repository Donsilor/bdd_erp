<?php

namespace addons\Finance\services;
use addons\Finance\common\models\ContractPay;
use common\helpers\Url;

/**
 * Class FinanceService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ContractPayService extends ContractPay
{
    /**
     * 采购单菜单
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'基础信息','url'=>Url::to(['contract-pay/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            3=>['name'=>'日志信息','url'=>Url::to(['contract-pay/log','id'=>$id, 'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }

 
}