<?php

namespace addons\Style\services;

use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\IsApply;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\QibanAttribute;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\SnHelper;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class QibanService extends Service
{
    public function createQiban($goods ,$attr_list){
        $qiban = new Qiban();
        $qiban->qiban_sn = SnHelper::createQibanSn();
        $qiban->audit_status = AuditStatusEnum::PENDING;
        $qiban->status = StatusEnum::DISABLED;
        $qiban->is_apply = IsApply::Wait;
        $qiban->attributes = $goods;

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
        //更新布产单属性到布产单横向字段
        if(false === $qiban->save(true)) {
            throw new \Exception($this->getError($qiban));
        }

        return $qiban ;
    }

    
}