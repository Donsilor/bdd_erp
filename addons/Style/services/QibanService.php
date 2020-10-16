<?php

namespace addons\Style\services;

use addons\Style\common\models\Qiban;
use addons\Style\common\models\QibanAttribute;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\Style;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class QibanService extends Service
{
    /**
     * 创建起版信息
     * @param unknown $goods
     * @param unknown $attr_list
     * @throws \Exception
     * @return \addons\Style\common\models\Qiban
     */
    public function createQiban($goods ,$attr_list){
        
        $model = new Qiban();        
        $model->status = StatusEnum::DISABLED;
        $model->attributes = $goods; 
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        foreach ($attr_list as $attr){
            $attrModel = new QibanAttribute();
            $attrModel->attributes = $attr;
            $attrModel->qiban_id = $model->id;
            if(false === $attrModel->save()){
                throw new \Exception($this->getError($attrModel));
            }
        }
        $this->createQibanSn($model); 
        return $model ;
    }
    
    public function isExist($qiban_sn = null){
        if($qiban_sn == null) return false;
        $qiban = Qiban::find()->where(['qiban_sn'=>$qiban_sn])->select(['id'])->one();
        return $qiban;
    }
    
    /**
     * 创建起版款号
     * @param Qiban $model
     * @param string $save
     */
    public function createStyleSn($model) 
    {
        if(!$model->id) {
            throw new \Exception("起版ID不能为空");
        }        
        if($model->style_id >0 || $model->qiban_type != QibanTypeEnum::NO_STYLE) {
            return ;
        }
        $style_material = $model->getStyleMaterial();
        if($style_material === false) {
            throw new \Exception("请完善起版信息中的材质");
        }
        if($model->style_channel_id == '') {
            throw new \Exception("请完善起版信息中的款式渠道");
        }else if(!in_array($model->style_channel_id,[3,12,16])) {
            //非 国际批发，国内电商，国际电商 不用创建款号
            return;
        }
        if($model->style_cate_id == '') {
            throw new \Exception("请完善起版信息中的款式分类");
        }
        if($model->style_sex == '') {
            throw new \Exception("请完善起版信息中的款式性别");
        }
        $style = new Style();
        $style->style_name = $model->qiban_name;
        $style->style_channel_id = $model->style_channel_id;
        $style->style_sex = $model->style_sex;
        $style->style_cate_id = $model->style_cate_id;
        $style->product_type_id = $model->product_type_id;        
        $style->style_material = $style_material;
        $style->status = -2;
        if(false === $style->save(true)) {
            throw new \Exception("创建款号失败:".$this->getError($style));
        }
        
        $model->style_sn = \Yii::$app->styleService->style->createStyleSn($style,true);
        $model->style_id = $style->id;
        
        if(false === $model->save(true,['style_id','style_sn'])) {
            throw new \Exception("更新款号失败:".$this->getError($model));
        }
        
        return true;
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
        $qiban_sn = "QB".date("ymd",strtotime($date)).str_pad($number, 3,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->qiban_sn = $qiban_sn;
            if(false == $model->save(true,['qiban_sn'])) {
                throw new \Exception($this->getError($model)); 
            }
        }
        return $model->qiban_sn;        
    }

    
}