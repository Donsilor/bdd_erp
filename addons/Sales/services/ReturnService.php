<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Sales\services;

use common\helpers\Url;

class ReturnService
{

    /**
     * 退款单 tab
     * @param int $return_id 快递公司ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($return_id, $returnUrl = null)
    {
        return [
            1 => ['name' => '退款单详情', 'url' => Url::to(['return/view', 'id' => $return_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            //2=>['name'=>'快递配送区域','url'=>Url::to(['express-area/index','express_id'=>$return_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * @param $model
     * @throws \Exception
     * 绑定现货
     */
    public function return($model)
    {

    }


}