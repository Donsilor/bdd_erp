<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Sales\services;

use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Express;
use common\helpers\Url;

class ExpressService
{

    /**
     * 快递公司 tab
     * @param int $express_id 快递公司ID
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($express_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'快递公司','url'=>Url::to(['express/view','id'=>$express_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'快递配送区域','url'=>Url::to(['express-area/index','express_id'=>$express_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * 下拉
     * @return array
     */
    public function getDropDown(){
        $model = Express::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->select(['id','name'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();

        return ArrayHelper::map($model,'id', 'name');
    }


}