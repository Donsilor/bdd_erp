<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;

use addons\Style\common\enums\LogTypeEnum;
use addons\Style\common\models\PartsStyle;
use addons\Supply\common\enums\PeijianStatusEnum;
use addons\Supply\common\models\Factory;
use addons\Supply\common\models\Produce;
use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceLog;
use addons\Supply\common\models\ProduceParts;
use addons\Supply\common\models\ProduceShipment;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\helpers\Url;
use yii\base\Exception;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\LogModuleEnum;
use addons\Supply\common\enums\BuChanEnum;
use addons\Supply\common\enums\PeiliaoStatusEnum;
use addons\Supply\common\enums\PeishiStatusEnum;
use addons\Supply\common\models\Peishi;
use addons\Style\common\enums\StonePositionEnum;
use addons\Supply\common\models\ProduceStone;
use addons\Supply\common\models\ProduceGold;
use addons\Supply\common\enums\PeiliaoTypeEnum;

class ProduceService extends Service
{
    public $switchQueue = false;

    public function queue($switchQueue = true)
    {
        $this->switchQueue = $switchQueue;
        return $this;
    }

    /**
     * 布产编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($produce_id, $returnUrl = null)
    {

        $menus = [
            1 => ['name' => '基础信息', 'url' => Url::to(['produce/view', 'id' => $produce_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            2 => ['name' => '金料信息', 'url' => Url::to(['produce-gold/index', 'produce_id' => $produce_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
            3 => ['name' => '石料信息', 'url' => Url::to(['produce-stone/index', 'produce_id' => $produce_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
            4 => ['name' => '配件信息', 'url' => Url::to(['produce-parts/index', 'produce_id' => $produce_id, 'tab' => 4, 'returnUrl' => $returnUrl])],
            5 => ['name' => '出厂信息', 'url' => Url::to(['produce-shipment/index', 'produce_id' => $produce_id, 'tab' => 5, 'returnUrl' => $returnUrl])],
            6 => ['name' => '日志信息', 'url' => Url::to(['produce-log/index', 'produce_id' => $produce_id, 'tab' => 6, 'returnUrl' => $returnUrl])]
        ];

        $model = Produce::find()->where(['id' => $produce_id])->one();
        if ($model->peiliao_status == PeiliaoStatusEnum::NONE) {
            unset($menus[2]);
        }
        if ($model->peishi_status == PeishiStatusEnum::NONE) {
            unset($menus[3]);
        }
        if ($model->peijian_status == PeijianStatusEnum::NONE) {
            unset($menus[4]);
        }

        return $menus;
    }

    /**
     * 创建布产单
     * @return array
     * @throws
     */
    public function createSyncProduce($goods, $attr_list)
    {

        $produce_id = $goods['id'] ?? 0;
        $is_new = true;
        if ($produce_id) {
            $is_new = false;
            $produce = Produce::findOne($produce_id);
            if (!$produce) {
                throw new \Exception("[{$produce_id}]布产单查询失败");
            }
        } else {
            $produce = new Produce();
            $produce->produce_sn = SnHelper::createProduceSn();
        }

        $produce->attributes = $goods;

        if (false === $produce->save()) {
            throw new \Exception($this->getError($produce));
        }

        ProduceAttribute::deleteAll(['produce_id' => $produce_id]);
        foreach ($attr_list as $attr) {
            $produceAttr = new ProduceAttribute();
            $produceAttr->attributes = $attr;
            $produceAttr->produce_id = $produce->id;
            if (false === $produceAttr->save()) {
                throw new \Exception($this->getError($produceAttr));
            }
            if ($produceAttr->attr_id == AttrIdEnum::INLAY_METHOD) {
                $produce->inlay_type = $produceAttr->attr_value_id;
            }
        }

        $produce->follower_name = $produce->follower->username ?? '';
        //更新布产单属性到布产单横向字段
        if (false === $produce->save(true)) {
            throw new \Exception($this->getError($produce));
        }
        if ($is_new === false && $produce->peiliao_type != PeiliaoTypeEnum::None) {
            //更新配石配料信息
            $this->updatePeiliao($produce);
        }

        if ($is_new === true) {
            $follower_name = $produce->follower ? $produce->follower->username : '';
            $supplier_name = $produce->supplier ? $produce->supplier->supplier_name : '';
            $log = [
                'produce_id' => $produce->id,
                'produce_sn' => $produce->produce_sn,
                'log_type' => LogTypeEnum::SYSTEM,
                'bc_status' => $produce->bc_status,
                'log_module' => '布产单创建',
                'log_msg' => "采购单审核生成布产单{$produce->produce_sn},供应商:{$supplier_name},跟单人:{$follower_name}"
            ];
            \Yii::$app->supplyService->produce->createProduceLog($log);
        }

        return $produce;
    }

    /**
     * 统计布产单出货数量
     * @param $produce_id
     * @return mixed
     */
    public function getShippentNum($produce_id)
    {
        return ProduceShipment::find()->where(['produce_id' => $produce_id])->sum('shippent_num') ?? 0;
    }

    /**
     * 创建配料单
     * @param Produce $form
     * @throws
     */
    public function createPeiliao($form)
    {
        if ($form->bc_status != BuChanEnum::TO_PEILIAO) {
            throw new \Exception('布产单不是' . BuChanEnum::getValue(BuChanEnum::TO_PEILIAO) . '状态，不能操作');
        }
        $attrValues = ArrayHelper::map($form->attrs ?? [], 'attr_id', 'attr_value');
        $form->bc_status = BuChanEnum::IN_PEILIAO;

        if ($form->peiliao_status == PeiliaoStatusEnum::PENDING) {
            $form->peiliao_status = PeiliaoStatusEnum::IN_PEILIAO;
            $this->createProduceGold($form, $attrValues);
        }
        if ($form->peishi_status == PeishiStatusEnum::PENDING) {
            $form->peishi_status = PeishiStatusEnum::IN_PEISHI;
            $this->createProduceStone($form, $attrValues);
        }
        if ($form->peijian_status == PeijianStatusEnum::PENDING) {
            $form->peijian_status = PeijianStatusEnum::IN_PEIJIAN;
            $this->createProduceParts($form, $attrValues);
        }
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

    /**
     * 更新配料单
     * @param Produce $form
     * @throws
     */
    public function updatePeiliao($form)
    {
        $attrValues = ArrayHelper::map($form->attrs ?? [], 'attr_id', 'attr_value');
        $this->createProduceGold($form, $attrValues);
        $this->createProduceStone($form, $attrValues);
        $this->createProduceParts($form, $attrValues);
    }

    /**
     * 创建配料单
     * @param Produce $form
     * @throws
     */
    private function createProduceGold($form, $attrValues)
    {
        $is_new = false;
        $reset = false;
        $attrValues = array_filter($attrValues);
        $gold = [
            'supplier_id' => $form->supplier_id,
            'gold_type' => $attrValues[AttrIdEnum::MATERIAL] ?? '',
            'gold_weight' => $form->goods_num * ($attrValues[AttrIdEnum::JINZHONG] ?? 0),
        ];
        $model = ProduceGold::find()->where(['produce_id' => $form->id])->one();
        if (!$model) {
            $model = new ProduceGold();
            $model->attributes = $gold;
            $model->produce_id = $form->id;
            $model->produce_sn = $form->produce_sn;
            $model->from_order_sn = $form->from_order_sn;
            $model->from_type = $form->from_type;
            $model->peiliao_status = ($form->peiliao_status == PeiliaoStatusEnum::NONE) ? PeiliaoStatusEnum::NONE : PeiliaoStatusEnum::IN_PEILIAO;
            $is_new = true;
        } else {

            if ($model->peiliao_status == PeiliaoStatusEnum::HAS_LINGLIAO) {
                //已领料禁止更新
                return;
            }
            $fields = ['gold_type', 'gold_weight'];
            //如果有重要字段变动，配石状态还原成 配石中
            if ($form->peiliao_status == PeiliaoStatusEnum::NONE) {
                $model->peiliao_status = PeiliaoStatusEnum::NONE;
                $form->peiliao_status = PeiliaoStatusEnum::NONE;
                $reset = true;
            } else {
                foreach ($fields as $field) {
                    if ($model->{$field} != $gold[$field]) {
                        $model->peiliao_status = PeiliaoStatusEnum::IN_PEILIAO;
                        $form->peiliao_status = PeiliaoStatusEnum::IN_PEILIAO;
                        $reset = true;
                        break;
                    }
                }
            }
            $model->attributes = ArrayHelper::merge($model->attributes, $gold);
        }
        if (false === $model->save()) {
            throw new \Exception($this->getError($model));
        }
        //重置配料单
        if ($reset === true) {
            if (false === $form->save(true, ['id', 'peiliao_status'])) {
                throw new \Exception($this->getError($form));
            }
        }

        //日志
        $log = [
            'produce_id' => $form->id,
            'produce_sn' => $form->produce_sn,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'bc_status' => $form->bc_status,
            'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PEILIAO),
            'log_msg' => ($is_new ? "生成" : "更新") . "配料单:" . $model->id,
        ];
        $this->createProduceLog($log);
    }

    /**
     * 创建配石单
     * @param Produce $form
     * @throws
     */
    private function createProduceStone($form, $attrValues)
    {
        $attrValues = array_filter($attrValues);
        $stone_list = [];
        //主石
        if (!empty($attrValues[AttrIdEnum::MAIN_STONE_TYPE])) {
            $stone_weight = $form->goods_num * ($attrValues[AttrIdEnum::MAIN_STONE_NUM] ?? 0) * ($attrValues[AttrIdEnum::MAIN_STONE_WEIGHT] ?? 0);
            $stone_list[StonePositionEnum::MAIN_STONE] = [
                'stone_type' => $attrValues[AttrIdEnum::MAIN_STONE_TYPE] ?? '',
                'stone_position' => StonePositionEnum::MAIN_STONE,
                'stone_num' => $form->goods_num * ($attrValues[AttrIdEnum::MAIN_STONE_NUM] ?? 0),
                'stone_weight' => $stone_weight,
                'stone_spec' => $attrValues[AttrIdEnum::DIA_SPEC] ?? '',
                'secai' => $attrValues[AttrIdEnum::MAIN_STONE_SECAI] ?? '',
                'carat' => $attrValues[AttrIdEnum::MAIN_STONE_WEIGHT] ?? 0,
                'color' => $attrValues[AttrIdEnum::DIA_COLOR] ?? '',
                'clarity' => $attrValues[AttrIdEnum::DIA_CLARITY] ?? '',
                'shape' => $attrValues[AttrIdEnum::DIA_SHAPE] ?? '',
                'cert_type' => $attrValues[AttrIdEnum::DIA_CERT_TYPE] ?? '',
                'cert_no' => $attrValues[AttrIdEnum::DIA_CERT_NO] ?? '',
            ];
        }
        //副石1
        if (!empty($attrValues[AttrIdEnum::SIDE_STONE1_TYPE])) {
            $stone_weight = $form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? 0);
            $stone_list[StonePositionEnum::SECOND_STONE1] = [
                'stone_type' => $attrValues[AttrIdEnum::SIDE_STONE1_TYPE] ?? '',
                'stone_position' => StonePositionEnum::SECOND_STONE1,
                'stone_num' => $form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE1_NUM] ?? 0),
                'stone_weight' => $stone_weight,
                'stone_spec' => $attrValues[AttrIdEnum::SIDE_STONE1_SPEC] ?? '',
                'shape' => $attrValues[AttrIdEnum::SIDE_STONE1_SHAPE] ?? '',
                'secai' => $attrValues[AttrIdEnum::SIDE_STONE1_SECAI] ?? '',
                'carat' => $attrValues[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? 0,
                'color' => $attrValues[AttrIdEnum::SIDE_STONE1_COLOR] ?? '',
                'clarity' => $attrValues[AttrIdEnum::SIDE_STONE1_CLARITY] ?? '',
            ];
        }
        //副石2
        if (!empty($attrValues[AttrIdEnum::SIDE_STONE2_TYPE])) {
            $stone_weight = $form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE2_WEIGHT] ?? 0);
            $stone_list[StonePositionEnum::SECOND_STONE2] = [
                'stone_type' => $attrValues[AttrIdEnum::SIDE_STONE2_TYPE] ?? '',
                'stone_position' => StonePositionEnum::SECOND_STONE2,
                'stone_num' => $form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE2_NUM] ?? 0),
                'stone_weight' => $stone_weight,
                'stone_spec' => $attrValues[AttrIdEnum::SIDE_STONE2_SPEC] ?? '',
                'shape' => $attrValues[AttrIdEnum::SIDE_STONE2_SHAPE] ?? '',
                'secai' => $attrValues[AttrIdEnum::SIDE_STONE2_SECAI] ?? '',
                'carat' => $attrValues[AttrIdEnum::SIDE_STONE2_WEIGHT] ?? 0,
                'color' => $attrValues[AttrIdEnum::SIDE_STONE2_COLOR] ?? '',
                'clarity' => $attrValues[AttrIdEnum::SIDE_STONE2_CLARITY] ?? '',
            ];
        }
        //副石3
        if (!empty($attrValues[AttrIdEnum::SIDE_STONE3_TYPE])) {
            $stone_weight = $form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE3_WEIGHT] ?? 0);
            $stone_list[StonePositionEnum::SECOND_STONE3] = [
                'stone_type' => $attrValues[AttrIdEnum::SIDE_STONE3_TYPE] ?? '',
                'stone_position' => StonePositionEnum::SECOND_STONE3,
                'stone_num' => $form->goods_num * ($attrValues[AttrIdEnum::SIDE_STONE3_NUM] ?? 0),
                'stone_weight' => $stone_weight,
                'stone_spec' => $attrValues[AttrIdEnum::SIDE_STONE3_SPEC] ?? '',
                'carat' => $attrValues[AttrIdEnum::SIDE_STONE3_WEIGHT] ?? 0,
                'color' => '',
                'clarity' => '',
            ];
        }
        $log_msgs = [];
        $fields = ['stone_type', 'stone_num', 'color'];
        $reset = false;
        foreach ($stone_list as $position => $stone) {
            $is_new = false;
            $model = ProduceStone::find()->where(['produce_id' => $form->id, 'stone_position' => $position])->one();
            if (!$model) {
                $model = new ProduceStone();
                $model->attributes = $stone;
                $model->produce_id = $form->id;
                $model->produce_sn = $form->produce_sn;
                $model->from_order_sn = $form->from_order_sn;
                $model->from_type = $form->from_type;
                $model->peishi_status = ($form->peishi_status == PeishiStatusEnum::NONE) ? PeishiStatusEnum::NONE : PeishiStatusEnum::IN_PEISHI;
                $is_new = true;
            } else {
                if ($model->peishi_status == PeishiStatusEnum::HAS_LINGSHI) {
                    //已领石，禁止更新
                    return;
                }
                //如果有重要字段变动，配石状态还原成 配石中
                if ($form->peishi_status == PeishiStatusEnum::NONE) {
                    $model->peishi_status = PeishiStatusEnum::NONE;
                    $form->peishi_status = PeishiStatusEnum::NONE;
                    $reset = true;
                } else {
                    foreach ($fields as $field) {
                        if ($model->{$field} != $stone[$field]) {
                            $model->peishi_status = PeishiStatusEnum::IN_PEISHI;
                            $form->peishi_status = PeishiStatusEnum::IN_PEISHI;
                            $reset = true;
                            break;
                        }
                    }
                }
                $model->attributes = ArrayHelper::merge($model->attributes, $stone);
            }
            $model->supplier_id = $form->supplier_id;//加工商
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            if ($is_new) {
                $log_msgs['生成配石单'][$model->id] = $model->id;
            } else {
                $log_msgs['更新配石单'][$model->id] = $model->id;
            }
        }
        //重置配石单
        if ($reset === true) {
            if (false === $form->save(true, ['id', 'peishi_status'])) {
                throw new \Exception($this->getError($form));
            }
        }
        $log_msg = '';
        foreach ($log_msgs as $k => $v) {
            $log_msg .= "{$k}:" . implode(",", array_keys($v)) . ' ';
        }
        //日志
        $log = [
            'produce_id' => $form->id,
            'produce_sn' => $form->produce_sn,
            'log_type' => LogTypeEnum::ARTIFICIAL,
            'bc_status' => $form->bc_status,
            'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PEILIAO),
            'log_msg' => $log_msg
        ];
        $this->createProduceLog($log);
    }

    /**
     * 创建配件单
     * @param Produce $form
     * @param array $attrValues
     * @throws
     */
    private function createProduceParts($form, $attrValues)
    {
        $is_new = false;
        $reset = false;
        if (!empty($form->parts_info)) {
            $parts_info = unserialize($form->parts_info)??[];
            if (is_array($parts_info)) {
                foreach ($parts_info as $item) {
                    $style_sn = $item['style_sn'] ?? "";
                    $style = PartsStyle::findOne(['style_sn'=>$style_sn]);
                    $parts = [
                        'supplier_id' => $form->supplier_id,
                        'style_sn' => $style_sn,
                        'parts_name' => $style->parts_name ?? "",
                        'parts_type' => $style->parts_type ?? "",
                        'material_type' => $style->metal_type ?? '',
                        'parts_num' => $form->goods_num * ($item['parts_num'] ?? 0),
                        'parts_weight' => $form->goods_num * ($item['parts_gold_weight'] ?? 0),
                    ];
                    $model = ProduceParts::find()->where(['produce_id' => $form->id, 'style_sn' => $style_sn])->one();
                    //$model = new ProduceParts();
                    if (!$model) {
                        $model = new ProduceParts();
                        $model->attributes = $parts;
                        $model->produce_id = $form->id;
                        $model->produce_sn = $form->produce_sn;
                        $model->from_order_sn = $form->from_order_sn;
                        $model->from_type = $form->from_type;
                        $model->peijian_status = ($form->peijian_status == PeijianStatusEnum::NONE) ? PeijianStatusEnum::NONE : PeijianStatusEnum::IN_PEIJIAN;
                        $is_new = true;
                    } else {
                        if ($model->peijian_status == PeijianStatusEnum::HAS_LINGJIAN) {
                            //已领料禁止更新
                            return;
                        }
                        $fields = ['parts_name', 'parts_type', 'material_type', 'parts_num', 'parts_weight'];
                        //如果有重要字段变动，配件状态还原成 配件中
                        if ($form->peijian_status == PeijianStatusEnum::NONE) {
                            $model->peijian_status = PeijianStatusEnum::NONE;
                            $form->peijian_status = PeijianStatusEnum::NONE;
                            $reset = true;
                        } else {
                            foreach ($fields as $field) {
                                if ($model->{$field} != $parts[$field]) {
                                    $model->peijian_status = PeijianStatusEnum::IN_PEIJIAN;
                                    $form->peijian_status = PeijianStatusEnum::IN_PEIJIAN;
                                    $reset = true;
                                    break;
                                }
                            }
                        }
                        $model->attributes = ArrayHelper::merge($model->attributes, $parts);
                    }
                    if (false === $model->save()) {
                        throw new \Exception($this->getError($model));
                    }
                }
                //日志
                $log = [
                    'produce_id' => $form->id,
                    'produce_sn' => $form->produce_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'bc_status' => $form->bc_status,
                    'log_module' => LogModuleEnum::getValue(LogModuleEnum::TO_PEILIAO),
                    'log_msg' => ($is_new ? "生成" : "更新") . "配件单:" . $model->id,
                ];
                $this->createProduceLog($log);
            }
        }
        //重置配件单
        if ($reset === true) {
            if (false === $form->save(true, ['id', 'peijian_status'])) {
                throw new \Exception($this->getError($form));
            }
        }

    }

    /**
     * 创建布产日志
     * @return array
     * @throws
     * @return
     */
    public function createProduceLog($log)
    {

        $model = new ProduceLog();
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id;
        $model->creator = \Yii::$app->user->identity->username;
        if (false === $model->save()) {
            throw new \Exception($this->getError($model));
        }
        return $model;
    }

    /**
     * 批量更新布产单，配石状态
     * @param array $produce_sns
     * @throws
     * @return
     */
    public function autoPeishiStatus(array $produce_sns)
    {
        if (!empty($produce_sns)) {
            //同步更新布产单配石状态
            $sql = "update " . Produce::tableName() . " p set peishi_status = (select min(peishi_status) from " . ProduceStone::tableName() . " ps where ps.produce_sn=p.produce_sn) where p.produce_sn in('" . implode("','", $produce_sns) . "')";
            return \Yii::$app->db->createCommand($sql)->execute();
        }
        return false;
    }

    /**
     * 批量更新布产单，配石状态
     * @param array $produce_sns
     * @throws
     * @return
     */
    public function autoPeiliaoStatus(array $produce_sns)
    {
        if (!empty($produce_sns)) {
            //同步更新布产单配石状态
            $sql = "update " . Produce::tableName() . " p set peiliao_status = (select min(pg.peiliao_status) from " . ProduceGold::tableName() . " pg where pg.produce_sn=p.produce_sn) where p.produce_sn in('" . implode("','", $produce_sns) . "')";
            return \Yii::$app->db->createCommand($sql)->execute();
        }
        return false;
    }

    /**
     * 批量更新布产单，配件状态
     * @param array $produce_sns
     * @throws
     * @return
     */
    public function autoPeijianStatus(array $produce_sns)
    {
        if (!empty($produce_sns)) {
            //同步更新布产单配件状态
            $sql = "update " . Produce::tableName() . " p set peijian_status = (select min(pg.peijian_status) from " . ProduceParts::tableName() . " pg where pg.produce_sn=p.produce_sn) where p.produce_sn in('" . implode("','", $produce_sns) . "')";
            return \Yii::$app->db->createCommand($sql)->execute();
        }
        return false;
    }
}