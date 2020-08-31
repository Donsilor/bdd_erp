<?php

namespace addons\Warehouse\services;

use phpDocumentor\Reflection\Types\Boolean;
use Yii;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\StoneStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use yii\db\Expression;
use common\helpers\Url;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneService extends Service
{
    /**
     * 石料库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1=>['name'=>'石料详情','url'=>Url::to(['stone/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'领石信息','url'=>Url::to(['stone/lingshi','id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3=>['name'=>'石料日志','url'=>Url::to(['stone-log/index','id'=>$id,'tab'=>3,'returnUrl'=>$returnUrl])],
        ];
        return $tabList;
    }
    /**
     * 创建石包号
     * @param WarehouseStone $model
     * @param Bool $save
     * @throws
     *
     */
    public function createStoneSn($model, $save = true)
    {
        //1.供应商
        $stone_sn = $model->supplier->supplier_tag ?? '00';
        //2.石料类型
        $type_codes = Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE,'id','code');
        $stone_sn .= $type_codes[$model->stone_type] ?? '0';
        //3.数字编号
        $stone_sn .= str_pad($model->id,7,'0',STR_PAD_LEFT);
        if($save === true) {
            $model->stone_sn = $stone_sn;
            if(false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $stone_sn;
    }
    /**
     * 更新库存信息
     * @param int $id
     * @throws
     *
     */
    public function updateStockCnt($id){
        $stone = WarehouseStone::findOne($id);
        $stock_cnt = $stone->ms_cnt
            +$stone->fenbaoru_cnt
            -$stone->ss_cnt
            -$stone->fenbaochu_cnt
            +$stone->ts_cnt
            -$stone->ys_cnt
            -$stone->sy_cnt
            -$stone->th_cnt
            +$stone->rk_cnt
            -$stone->ck_cnt;
        $stock_weight = $stone->ms_weight
            +$stone->fenbaoru_weight
            -$stone->ss_weight
            -$stone->fenbaochu_weight
            +$stone->ts_weight
            -$stone->ys_weight
            -$stone->sy_weight
            -$stone->th_weight
            +$stone->rk_weight
            -$stone->ck_weight;
        $stone->stock_cnt = $stock_cnt;
        $stone->ck_weight = $stock_weight;
        if(false === $stone->save()){
            throw new \Exception($this->getError($stone));
        }
    }
    /**
     * 更改石料库存
     * @param string $stone_sn
     * @param integer $adjust_num 调整数量
     * @param double $adjust_weight 调整重量
     * @param integer $adjust_type 调整类型 1增加 0减
     * @throws
     *
     */
    public function adjustStoneStock($stone_sn,$adjust_num ,$adjust_weight, $adjust_type) {

        $adjust_num = abs(floatval($adjust_num));
        $adjust_weight = abs(floatval($adjust_weight));
        
        $model = WarehouseStone::find()->where(['stone_sn'=>$stone_sn])->one();
        if(empty($model)) {
            throw new \Exception("({$stone_sn})石包编号不存在");
        }elseif ($model->stone_status != StoneStatusEnum::IN_STOCK && $model->stone_status != StoneStatusEnum::SOLD_OUT) {
            throw new \Exception("({$stone_sn})石包不是库存中");
        }elseif($adjust_type == AdjustTypeEnum::MINUS){
            if($model->stock_cnt < $adjust_num) {
                throw new \Exception("({$stone_sn})石包库存不足：数量不足");
            }elseif($model->stock_weight < $adjust_weight) {
                throw new \Exception("({$stone_sn})石包库存不足：重量不足");
            }
        }        
        if($adjust_weight <= 0){
            throw new \Exception("({$stone_sn})石包调整重量不能小于或等于0");
        }
        if($adjust_type == AdjustTypeEnum::ADD) {
            $update = ["stock_cnt"=>new Expression("stock_cnt+{$adjust_num}"), "stock_weight" =>new Expression("stock_weight+{$adjust_weight}"),'stone_status'=>StoneStatusEnum::IN_STOCK];
            $where  = new Expression("stone_sn='{$stone_sn}'");
            $result = WarehouseStone::updateAll($update,$where);
            if(!$result) {
                throw new \Exception("({$stone_sn})石包库存变更失败(新增)");
            }
        }else{
            $update = ["stock_cnt"=>new Expression("stock_cnt-{$adjust_num}"), "stock_weight" =>new Expression("stock_weight-{$adjust_weight}")];
            $where  = new Expression("stone_sn='{$stone_sn}' and stock_cnt >={$adjust_num} and stock_weight>={$adjust_weight}");
            $result = WarehouseStone::updateAll($update,$where);
            if(!$result) {
                throw new \Exception("({$stone_sn})石包库存变更失败(库存不足)");
            }
            //更新为已售馨
            if($model->stock_cnt <= $adjust_num) {
                WarehouseStone::updateAll(['stone_status'=>StoneStatusEnum::SOLD_OUT],new Expression("stone_sn='{$stone_sn}' and stock_cnt <= 0"));
            }
        }
        
    }


}