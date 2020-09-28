<?php

namespace addons\Style\services;

use addons\Style\common\enums\IsApply;
use addons\Style\common\enums\QibanSourceEnum;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\QibanAttribute;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class QibanService extends Service
{
    /**
     * 创建起版号
     * @param unknown $goods
     * @param unknown $attr_list
     * @throws \Exception
     * @return \addons\Style\common\models\Qiban
     */
    public function createQiban($goods ,$attr_list){
        $qiban = new Qiban();
        $qiban->audit_status = AuditStatusEnum::PENDING;
        $qiban->status = StatusEnum::DISABLED;
        $qiban->is_apply = IsApply::Wait;
        $qiban->attributes = $goods;
        $qiban->qiban_source_id = QibanSourceEnum::BUSINESS_APPLI;
        $qiban->creator_id = \Yii::$app->user->identity->getId();
        $qiban->created_at = time();
        if(false === $qiban->save()){
            throw new \Exception($this->getError($qiban));
        }

        foreach ($attr_list as $attr){
            $qibanAttr = new QibanAttribute();
            $qibanAttr->attr_id = $attr['attr_id'];
            $qibanAttr->attr_values = $attr['attr_value'];
            $qibanAttr->sort = $attr['sort'];
            $qibanAttr->qiban_id = $qiban->id;
            if(false === $qibanAttr->save()){
                throw new \Exception($this->getError($qibanAttr));
            }
        }
        /* //更新布产单属性到布产单横向字段
        if(false === $qiban->save(true)) {
            throw new \Exception($this->getError($qiban));
        } */
        $this->createQibanSn($model); 
        return $qiban ;
    }
    
    public function isExist($qiban_sn = null){
        if($qiban_sn == null) return false;
        $qiban = Qiban::find()->where(['qiban_sn'=>$qiban_sn])->select(['id'])->one();
        return $qiban;
    }
    /**
     * 创建起版号编码
     * @param Qiban $model
     * @param string $save
     */
    public function createQibanSn(& $model, $save = true)
    {
        if(!$model->id) {
            throw new \Exception("id 不能为空"); 
        }
        $date = date('Y-m-d', $model->created_at ? $model->created_at : time());        
        $where = [
                'and',
                ['<=','id',$model->id],
                ['>=','created_at',strtotime($date)],
                ['<=','created_at',strtotime($date." 23:59:59")]
        ];
        $number = Qiban::find()->where($where)->count();
        $number = $number ? $number : 1;
        $qiban_sn = "QB".date("md",strtotime($date)).str_pad($number, 3,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->qiban_sn = $qiban_sn;
            if(false == $model->save(true,['qiban_sn'])) {
                throw new \Exception($this->getError($model)); 
            }
        }
        return $model->qiban_sn;        
    }

    
}