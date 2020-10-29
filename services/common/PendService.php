<?php

namespace services\common;

use Yii;
use common\components\Service;
use common\models\common\Pend;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class PendService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class PendService extends Service
{

    /**
     * @param integer $uid
     * @return array
     */
    public static function getPendListByUid($uid = null)
    {
        $list = Pend::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['=', 'operor_id', $uid])
            ->select(['oper_type', 'oper_id', 'oper_sn', 'pend_status', 'created_at'])
            ->orderBy('created_at desc')
            ->asArray()
            ->limit(20)
            ->all();
        $pend_num = 0;
        if ($list) {
            foreach ($list as $item) {
                $day = date('m.d', $item['created_at']) ?? 0;
                $day = $day == date('m.d', time()) ? '今天' : $day;
                if (!$item['pend_status']) {
                    $pend_num++;
                }
                $data[$day][] = $item;
            }
        }
        return ['list' => $data ?? [], 'num' => $pend_num ?? 0];
    }
}