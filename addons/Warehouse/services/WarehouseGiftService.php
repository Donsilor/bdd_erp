<?php

namespace addons\Warehouse\services;

use addons\Style\common\enums\StyleSexEnum;
use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\models\WarehouseGiftBill;
use addons\Style\common\models\Style;
use addons\Warehouse\common\enums\GiftStatusEnum;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\InputTypeEnum;
use yii\db\Expression;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGiftService extends Service
{
    /**
     *
     * 赠品库存tab
     * @param int $id ID
     * @param $returnUrl URL
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        $tabList = [
            1 => ['name' => '赠品详情', 'url' => Url::to(['gift/view', 'id' => $id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            2 => ['name' => '赠品订单', 'url' => Url::to(['gift-bill/index', 'id' => $id, 'tab' => 2, 'returnUrl' => $returnUrl])],
            3 => ['name' => '赠品日志', 'url' => Url::to(['gift-log/index', 'id' => $id, 'tab' => 3, 'returnUrl' => $returnUrl])],
        ];
        return $tabList;
    }

    /**
     *
     * 创建批次号
     * @param WarehouseGift $model
     * @param bool $save
     * @return
     * @throws
     */
    public function createGiftSn($model, $save = true)
    {
        //1.供应商
        //$gift_sn = $model->supplier->supplier_tag ?? '00';
        $gift_sn = 'ZP';
        //2.款式分类
        $cate_tag = $model->cate->tag ?? '00';
        $cate_tag_list = explode("-", $cate_tag);
        if(count($cate_tag_list) < 2 ) {
            throw new \Exception("编款失败：款式分类未配置编码规则");
        }
        list($cate_m, $cate_w) = $cate_tag_list;
        if($model->style_sex == StyleSexEnum::MAN) {
            $gift_sn .= $cate_m;
        }else {
            $gift_sn .= $cate_w;
        }
        //3.数字编号
        $gift_sn .= str_pad($model->id, 6, '0', STR_PAD_LEFT);
        if ($save === true) {
            $model->gift_sn = $gift_sn;
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
        }
        return $gift_sn;
    }

    /**
     * 商品图片
     * @param WarehouseGift $model
     * @return string
     * @throws
     */
    public function getStyleImage($model)
    {
        $style = Style::find()->where(['style_sn' => $model->style_sn])->one();
        $image = $style->style_image ?? '';
        return $image;
    }

    /**
     *
     * 更改赠品库存
     * @param string $gift_sn
     * @param double $adjust_weight
     * @param integer $adjust_type
     * @throws
     *
     */
    public function adjustGiftStock($gift_sn, $adjust_weight, $adjust_type)
    {

        $adjust_weight = abs(floatval($adjust_weight));

        $model = WarehouseGift::find()->where(['gift_sn' => $gift_sn])->one();
        if (empty($model)) {
            throw new \Exception("({$gift_sn})赠品编号不存在");
        } elseif ($model->gift_status != GiftStatusEnum::IN_STOCK && $model->gift_status != GiftStatusEnum::SOLD_OUT) {
            throw new \Exception("({$gift_sn})赠品不是库存中");
        } elseif ($adjust_type == AdjustTypeEnum::MINUS) {
            if ($model->gift_weight < $adjust_weight) {
                throw new \Exception("({$gift_sn})赠品库存不足");
            }
        }
        if ($adjust_weight <= 0) {
            throw new \Exception("({$gift_sn})赠品调整重量不能为0");
        }
        if ($adjust_type == AdjustTypeEnum::ADD) {
            $update = ['gift_weight' => new Expression("gift_weight+{$adjust_weight}"), 'gift_status' => GiftStatusEnum::IN_STOCK];
            $result = WarehouseGift::updateAll($update, new Expression("gift_sn='{$gift_sn}'"));
            if (!$result) {
                throw new \Exception("({$gift_sn})赠品库存变更失败");
            }
        } else {
            $update = ['gift_weight' => new Expression("gift_weight-{$adjust_weight}")];
            $result = WarehouseGift::updateAll($update, new Expression("gift_sn='{$gift_sn}' and gift_weight>={$adjust_weight}"));
            if (!$result) {
                throw new \Exception("({$gift_sn})赠品库存不足");
            }
            //更新为已售馨
            if ($model->gift_weight <= $adjust_weight) {
                $result = WarehouseGift::updateAll(['gift_status' => GiftStatusEnum::SOLD_OUT], new Expression("gift_sn='{$gift_sn}' and gift_weight <= 0"));
            }
        }

    }


    /***
     * 生成赠品出入库单
     * @throws
     */
    public function createBill($gift_bill_info)
    {
        $gift = WarehouseGift::find()->where(['id' => $gift_bill_info['gift_id']])->one();
        $gift->gift_num = $gift_bill_info['stock_num'];
        if (false === $gift->save(true, ['gift_num'])) {
            throw new \Exception($this->getError($gift));
        }

        $gift_bill = new WarehouseGiftBill();
        $gift_bill->attributes = $gift_bill_info;
        $gift_bill->creator_id = Yii::$app->user->identity->getId();
        $gift_bill->created_at = time();
        $gift_bill->updated_at = time();
        if (false === $gift_bill->save()) {
            throw new \Exception($this->getError($gift_bill));
        }
        return $gift_bill;
    }


    /**
     * @param $model
     * @return array
     * 赠品字段映射
     */
    public function getMapping()
    {

        $attr_list = array(
            //材质
            [
                'attr_id' => AttrIdEnum::MATERIAL_TYPE,
                'attr_field' => 'material_type',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0,

            ],
            //材质颜色
            [
                'attr_id' => AttrIdEnum::MATERIAL_COLOR,
                'attr_field' => 'material_color',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0,

            ],
            //手寸(美)
            [
                'attr_id' => AttrIdEnum::FINGER,
                'attr_field' => 'finger',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0

            ],
            //手寸(港)
            [
                'attr_id' => AttrIdEnum::PORT_NO,
                'attr_field' => 'finger_hk',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0

            ],
            //链长
            [
                'attr_id' => AttrIdEnum::CHAIN_LENGTH,
                'attr_field' => 'chain_length',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
            //主石类型
            [
                'attr_id' => AttrIdEnum::MAIN_STONE_TYPE,
                'attr_field' => 'main_stone_type',
                'input_type' => InputTypeEnum::INPUT_SELECT,
                'is_require' => 0
            ],
            //主石数量
            [
                'attr_id' => AttrIdEnum::MAIN_STONE_NUM,
                'attr_field' => 'main_stone_num',
                'input_type' => InputTypeEnum::INPUT_TEXT,
                'is_require' => 0
            ],
        );
        return $attr_list;
    }

}