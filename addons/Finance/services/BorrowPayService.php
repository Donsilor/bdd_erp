<?php

namespace addons\Finance\services;
use common\helpers\Url;

/**
 * Class FinanceService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class BorrowPayService extends FinanceService
{
    /**
     * 采购单菜单
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'基础信息','url'=>Url::to(['borrow-pay/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            3=>['name'=>'日志信息','url'=>Url::to(['borrow-pay/log','id'=>$id, 'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }

 
}