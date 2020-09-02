<?php

namespace addons\Gdzb\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Sales\common\forms\CustomerForm;

/**
 * Class CustomerService
 * @package services\common
 */
class CustomerService extends Service
{
    /**
     * 客户列表 tab
     * @param int $id 客户ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
            1=>['name'=>'客户信息','url'=>Url::to(['view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'订单信息','url'=>Url::to(['order', 'customer_id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * 创建客户编号
     * @param CustomerForm $model
     * @param bool $save
     * @return string $customer_no 客户编号
     * @throws
     */
    public function createCustomerNo($model, $save = true)
    {
        //1.渠道标签
        $customer_no = $model->channel->tag ?? '00';
        //2.数字编号
        $customer_no .= str_pad($model->id,8,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->customer_no = $customer_no;
            if(false === $model->save(true, ['customer_no'])) {
                throw new \Exception($this->getError($model));
            }
        }
        return $customer_no;
    }
}