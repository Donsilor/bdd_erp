<?php

namespace services\common;

use Yii;
use \common\helpers\Auth;
use common\components\Service;
use common\models\common\QuickConfig;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * Class PurchaseFqcConfigService
 * @package addons\Style\services
 * @author jianyan74 <751393839@qq.com>
 */
class QuickConfigService extends Service
{

    /**
     * 编辑获取下拉
     *
     * @param string $pid
     * @return array
     */
    public static function getDropDownForEdit($pid = '')
    {
        $data = self::getDropDown($pid);
        return ArrayHelper::merge([0 => '顶级分类'], $data);
    }

    /**
     * @param integer $pid
     * @return array
     */
    public static function getDropDown($pid = null)
    {
        $list = QuickConfig::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            ->andFilterWhere(['<>', 'id', $pid])
            ->select(['id', 'name', 'pid', 'level'])
            ->orderBy('sort asc')
            ->asArray()
            ->all();
        $models = ArrayHelper::itemsMerge($list);
        return ArrayHelper::map(ArrayHelper::itemsMergeDropDown($models, 'id', 'name'), 'id', 'name');

    }

    /**
     * 分组下拉框
     * @param integer $pid
     * @param integer $treeStat
     * @return array
     */
    public static function getGrpDropDown($pid = null, $treeStat = 1)
    {
        $query = QuickConfig::find()->alias('a')
            ->where(['status' => StatusEnum::ENABLED]);
        if ($pid !== null) {
            if ($pid == 0) {
                $query->andWhere(['a.pid' => $pid]);
            } else {
                $query->andWhere(['or', ['a.pid' => $pid], ['a.id' => $pid]]);
            }
        }
        $models = $query
            ->select(['id', 'name', 'pid', 'level'])
            ->orderBy('sort asc,created_at asc')
            ->asArray()
            ->all();
        return ArrayHelper::itemsMergeGrpDropDown($models, 0, 'id', 'name', 'pid', $treeStat);
    }

    /**
     * @param integer $uid
     * @return array
     */
    public static function getQuickInByUid($uid = null)
    {
        $models = QuickConfig::find()
            ->where(['=', 'status', StatusEnum::ENABLED])
            //->andFilterWhere(['=', 'operor_id', $uid])
            ->select(['id', 'name', 'code', 'url', 'pid', 'level'])
            ->orderBy('sort desc')
            ->asArray()
            ->all();
        $lists = ArrayHelper::itemsMergeGrpDropDown($models, 0, 'id', 'name', 'pid');
        $urls = ArrayHelper::map($models, 'id', 'url');
        $btns = ArrayHelper::map($models, 'id', 'code');
//        if ($lists && is_array($lists)) {
//            foreach ($lists as $u => $list) {
//                $is_show = true;
//                if ($list) {
//                    foreach ($list as $id => $p) {
//                        if (!Auth::verify('/' . $urls[$id]) || !Auth::verify($btns[$id] ?? "")) {
//                            $is_show = false;
//                        }
//                    }
//                }
//                if (!$is_show)  unset($lists[$u]);
//            }
//        }
        return ['list' => $lists ?? [], 'url' => $urls ?? [], 'btn' => $btns ?? []];
    }
}