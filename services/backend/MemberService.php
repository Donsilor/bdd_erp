<?php

namespace services\backend;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\models\backend\Member;
use common\models\backend\MemberPend;
use common\enums\PendStatusEnum;
use common\components\Service;

/**
 * Class MemberService
 * @package services\backend
 * @author jianyan74 <751393839@qq.com>
 */
class MemberService extends Service
{
    /**
     * 记录访问次数
     *
     * @param Member $member
     */
    public function lastLogin(Member $member)
    {
        $member->visit_count += 1;
        $member->last_time = time();
        $member->last_ip = Yii::$app->request->getUserIP();
        $member->save();
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return ArrayHelper::map($this->findAll(), 'id', 'username');
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll()
    {
        return Member::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();
    }
    /**
     * 查询用户列表
     * @param unknown $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAllByIds($ids) 
    {
        return Member::find()->select(['id','mobile','username'])
            ->where(['id' => $ids])
            ->all();
    }
    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByIdWithAssignment($id)
    {
        return Member::find()
            ->where(['id' => $id])
            ->with('assignment')
            ->one();
    }

    public function getDropDown(){

        $model = $this->findAll();
        return ArrayHelper::map($model,'id', 'username');
    }

    /**
     *
     * 用户待处理
     * @param integer $uid
     * @return array
     */
    public static function getPendListByUid($uid = null)
    {
        $query = MemberPend::find()
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