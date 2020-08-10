<?php

namespace addons\Warehouse\services;

use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseTemplet;
use addons\Style\common\models\Style;
use addons\Warehouse\common\enums\LayoutTypeEnum;
use addons\Warehouse\common\enums\TempletStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use yii\db\Expression;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseTempletService extends Service
{
    /**
     * 样板库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1 => ['name' => '样板详情', 'url' => Url::to(['templet/view', 'id' => $id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            //2=>['name'=>'领料信息','url'=>Url::to(['templet/lingliao','id'=>$id,'tab'=>2,'returnUrl'=>$returnUrl])],
            3 => ['name' => '样板日志', 'url' => Url::to(['templet-log/index', 'id' => $id, 'tab' => 3, 'returnUrl' => $returnUrl])],
        ];
        return $tabList;
    }

    /**
     * 创建批次号
     * @param WarehouseTemplet $model
     * @param bool $save
     * @return string
     * @throws
     */
    public function createBatchSn($model, $save = true)
    {
        //1.供应商
        $batch_sn = $model->supplier->supplier_tag ?? '00';
        //2.样板类型
        if ($model->layout_type == LayoutTypeEnum::SILVER) {
            $batch_sn .= "S";
        } elseif ($model->layout_type == LayoutTypeEnum::RUBBER) {
            $batch_sn .= "R";
        }
        //3.数字编号
        $batch_sn .= str_pad($model->id, 7, '0', STR_PAD_LEFT);
        if ($save === true) {
            $model->batch_sn = $batch_sn;
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $batch_sn;
    }

    /**
     * 商品图片
     * @param WarehouseTemplet $model
     * @return
     * @throws
     */
    public function getStyleImage($model)
    {
        $style = Style::find()->where(['style_sn' => $model->style_sn])->one();
        $image = $style->style_image ?? '';
        return $image;
    }

    /**
     * 更改库存
     * @param string $templet_sn
     * @param double $adjust_weight
     * @param integer $adjust_type
     * @throws
     *
     */
    public function adjustGoldStock($templet_sn, $adjust_weight, $adjust_type)
    {

        $adjust_weight = abs(floatval($adjust_weight));

        $model = WarehouseTemplet::find()->where(['templet_sn' => $templet_sn])->one();
        if (empty($model)) {
            throw new \Exception("({$templet_sn})样板编号不存在");
        } elseif ($model->templet_status != TempletStatusEnum::IN_STOCK && $model->templet_status != TempletStatusEnum::SOLD_OUT) {
            throw new \Exception("({$templet_sn})样板不是库存中");
        } elseif ($adjust_type == AdjustTypeEnum::MINUS) {
            if ($model->templet_weight < $adjust_weight) {
                throw new \Exception("({$templet_sn})样板库存不足");
            }
        }
        if ($adjust_weight <= 0) {
            throw new \Exception("({$templet_sn})样板调整重量不能为0");
        }
        if ($adjust_type == AdjustTypeEnum::ADD) {
            $update = ['templet_weight' => new Expression("templet_weight+{$adjust_weight}"), 'templet_status' => TempletStatusEnum::IN_STOCK];
            $result = WarehouseTemplet::updateAll($update, new Expression("templet_sn='{$templet_sn}'"));
            if (!$result) {
                throw new \Exception("({$templet_sn})样板库存变更失败");
            }
        } else {
            $update = ['templet_weight' => new Expression("templet_weight-{$adjust_weight}")];
            $result = WarehouseTemplet::updateAll($update, new Expression("templet_sn='{$templet_sn}' and templet_weight>={$adjust_weight}"));
            if (!$result) {
                throw new \Exception("({$templet_sn})样板库存不足");
            }
            //更新为已售馨
            if ($model->templet_weight <= $adjust_weight) {
                $result = WarehouseTemplet::updateAll(['templet_status' => TempletStatusEnum::SOLD_OUT], new Expression("templet_sn='{$templet_sn}' and templet_weight <= 0"));
            }
        }

    }

}