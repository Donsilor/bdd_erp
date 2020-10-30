<?php

namespace services\backend;

use Yii;
use common\components\Service;
use common\models\member\Pend;
use common\enums\PendStatusEnum;
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
        $query = Pend::find()
            ->select(['oper_type', 'oper_id', 'oper_sn', 'pend_status', 'created_at'])
            ->where(['=', 'status', StatusEnum::ENABLED]);
        $query->andFilterWhere(['=', 'operor_id', $uid]);
        $pendNum = clone $query;
        $pendNum->andFilterWhere(['=', 'pend_status', PendStatusEnum::PENDING]);
        $pend_num = $pendNum->count();
        $list = $query->orderBy('created_at desc')->limit(20)->asArray()->all();
        if ($list) {
            foreach ($list as $item) {
                $day = date('m.d', $item['created_at']) ?? 0;
                $day = $day == date('m.d', time()) ? '今天' : $day;
                $data[$day][] = $item;
            }
        }
        return ['list' => $data ?? [], 'num' => $pend_num ?? 0];
    }
}