<?php

namespace addons\Style\services;

use addons\Style\common\enums\StonePositionEnum;
use addons\Style\common\models\StyleStone;
use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleFactory;
use addons\Style\common\models\StyleFactoryFee;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\models\StyleImages;
use addons\Style\common\models\StyleGift;
use addons\Style\common\forms\StyleForm;
use addons\Style\common\enums\StyleMaterialEnum;
use addons\Style\common\enums\AttrValueIdEnum;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\AuditStatusEnum;
use common\enums\TargetTypeEnum;
use common\enums\ConfirmEnum;
use common\enums\StatusEnum;
use common\enums\AutoSnEnum;
use common\helpers\UploadHelper;
use common\helpers\StringHelper;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleService extends Service
{
    public $targetType = TargetTypeEnum::STYLE_STYLE;

    /**
     * 款式编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($style_id, $returnUrl = null)
    {
        $menus = [
            1 => ['name' => '基础信息', 'url' => Url::to(['style/view', 'id' => $style_id, 'tab' => 1, 'returnUrl' => $returnUrl])],
            2 => ['name' => '款式属性', 'url' => Url::to(['style-attribute/index', 'style_id' => $style_id, 'tab' => 2, 'returnUrl' => $returnUrl])],
            3 => ['name' => '商品列表', 'url' => Url::to(['style-goods/edit-all', 'style_id' => $style_id, 'tab' => 3, 'returnUrl' => $returnUrl])],
            4 => ['name' => '石头信息', 'url' => Url::to(['style-stone/index', 'style_id' => $style_id, 'tab' => 4, 'returnUrl' => $returnUrl])],
            5 => ['name' => '工厂信息', 'url' => Url::to(['style-factory/index', 'style_id' => $style_id, 'tab' => 5, 'returnUrl' => $returnUrl])],
            6 => ['name' => '工费信息', 'url' => Url::to(['style-factory-fee/index', 'style_id' => $style_id, 'tab' => 6, 'returnUrl' => $returnUrl])],
            7 => ['name' => '款式图片', 'url' => Url::to(['style-image/index', 'style_id' => $style_id, 'tab' => 7, 'returnUrl' => $returnUrl])],
            8 => ['name' => '日志信息', 'url' => Url::to(['style-log/index', 'style_id' => $style_id, 'tab' => 8, 'returnUrl' => $returnUrl])]
        ];

        $model = Style::find()->select(['id', 'is_inlay'])->where(['id' => $style_id])->one();
        if ($model && $model->is_inlay == ConfirmEnum::NO) {
            unset($menus[4]);
        }
        if ($model && $model->is_gift == ConfirmEnum::YES) {
            unset($menus[3], $menus[6]);
        }
        return $menus;
    }

    /**
     * 获取款式属性列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStyleAttrList($style_id)
    {
        return StyleAttribute::find()->where(['style_id' => $style_id])->asArray()->all();
    }

    /**
     * 创建款式编号
     * @param Style $model
     * @throws
     */
    public static function createStyleSn($model, $save = true)
    {
        if (!$model->id) {
            throw new \Exception("编款失败：款式ID不能为空");
        }
        $channel_tag = $model->channel->tag ?? null;
        if (empty($channel_tag)) {
            throw new \Exception("编款失败：款式渠道未配置编码规则");
        }
        //1.渠道部门代号
        $prefix = $channel_tag;
        //2.款式分类
        $cate_tag = $model->cate->tag ?? '';
        $cate_tag_list = explode("-", $cate_tag);
        if (count($cate_tag_list) < 2) {
            throw new \Exception("编款失败：款式分类未配置编码规则");
        }
        list($cate_m, $cate_w) = $cate_tag_list;
        if ($model->style_sex == StyleSexEnum::MAN) {
            $prefix .= $cate_m;
        } else {
            $prefix .= $cate_w;
        }
        //3.中间部分
        $middle = str_pad($model->id, 6, '0', STR_PAD_LEFT);
        //4.结尾部分-金属材质
        $last = $model->style_material;
        $model->style_sn = $prefix . $middle . $last;
        if ($save === true) {
            $model->is_autosn = AutoSnEnum::YES;
            $result = $model->save(true, ['id', 'style_sn', 'is_autosn']);
            if ($result === false) {
                throw new \Exception("编款失败：保存款号失败");
            }
        }
        return $model->style_sn;
    }

    public function getStyleImages($style_sn)
    {
        $list = StyleImages::find()->alias('a')
            ->innerJoin(Style::tableName() . ' s', 's.id=a.style_id')
            ->where(['s.style_sn' => $style_sn, 'a.status' => StatusEnum::ENABLED])
            ->select(['a.image'])
            ->asArray()
            ->all();
        if (empty($list)) return [];
        return array_column($list, 'image');
    }

    public function isExist($style_sn = null)
    {
        if ($style_sn == null) return false;
        $style = Style::find()->where(['style_sn' => $style_sn])->select(['id'])->one();
        return $style;
    }

    /**
     *
     * 同步创建赠品款式
     * @param Style $form
     * @throws
     */
    public static function createGiftStyle($form)
    {
        //$goods_size = \Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($form->style_sn, AttrIdEnum::PRODUCT_SIZE);
        //$chain_length = \Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($form->style_sn, AttrIdEnum::CHAIN_LENGTH);
        $gift = new StyleGift();
        $style = [
            'style_id' => $form->id,
            'style_sn' => $form->style_sn,
            'gift_name' => $form->style_name,
            'style_image' => $form->style_image,
            'style_cate_id' => $form->style_cate_id,
            'style_sex' => $form->style_sex,
            //'material_type' => '',
            //'material_color' => '',
            //'goods_size' => $goods_size??"",
            //'finger' => '',
            //'finger_hk' => '',
            //'chain_length' => $chain_length??"",
            'cost_price' => $form->cost_price,
            'market_price' => $form->market_price,
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];
        $gift->attributes = $style;
        if (false === $gift->save()) {
            throw new \Exception("创建赠品款式失败");
        }
    }

    /**
     * 批量导入
     * @param StyleForm $form
     * @throws
     */
    public function uploadStyles($form)
    {
        if (empty($form->file) && !isset($form->file)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'csv') {
            throw new \Exception("请上传csv格式文件");
        }
        if (!$form->file->tempName) {
            throw new \Exception("文件不能为空");
        }
        $file = fopen($form->file->tempName, 'r');
        $i = 0;
        $flag = true;
        $error_off = true;
        $error = $style_sns = $field = $styleList = $attrList = $factoryList1 = $factoryList2 = $styleFee = [];
        $creator_id = \Yii::$app->user->identity->getId();
        while ($style = fgetcsv($file)) {
            if (count($style) != 42) {
                throw new \Exception("模板格式不正确，请下载最新模板");
            }
            if ($i >= 501) {
                throw new \Exception("每次最多能导入500条数据");
            }
            if ($i <= 1) {
                if ($i == 1) {
                    $field = $form->formatField($style);
                    if ($field == false) {
                        throw new \Exception("表头格式不对[code=1]");
                    }
                    if (count($field) != 42) {
                        throw new \Exception("表头格式不对[code=2]");
                    }
                }
                $i++;
                continue;
            }
            $style = $form->trimField($style, $field);
            if ($style == false) {
                throw new \Exception("数据格式不对");
            }
            $style_name = $form->formatValue($style['style_name'] ?? "", "");
            $style_sn = $form->formatValue($style['style_sn'] ?? "", "");
            if (!empty($style_sn)) {
                if ($key = array_search($style_sn, $style_sns)) {
                    $flag = false;
                    $error[$i][] = "款号与第" . ($key + 1) . "行款号重复";
                }
                $style_sns[$i] = $style_sn;

                $styleModel = Style::findOne(['style_sn' => $style_sn]);
                if (!empty($styleModel)) {
                    $flag = false;
                    $error[$i][] = "款号在系统已存在，不能重复";
                }
                $is_autosn = ConfirmEnum::NO;
            } else {
                $flag = false;
                $error[$i][] = "款号不能为空";
                $is_autosn = ConfirmEnum::YES;
            }
            $styleAttr = $this->extendAttrByStyleSn($style_sn);//款号获取款式属性
            $style_cate_id = $form->formatValue($style['style_cate_id'] ?? 0, 0);
            if (empty($style_cate_id)) {
                $style_cate_id = $styleAttr['style_cate_id'] ?? "";
                if (empty($style_cate_id) && !empty($style_sn)) {
                    $flag = false;
                    $error[$i][] = "款号填写有误，无法获取款式分类";
                }
//                $flag = false;
//                $error[$i][] = "款式分类不能为空";
            } elseif (!is_numeric($style_cate_id)) {
                $flag = false;
                $error[$i][] = "款式分类填写有误";
            }
            if (empty($style_name)) {
                //$flag = false;
                //$error[$i][] = "款式名称不能为空";
                $styleCate = StringHelper::strToChineseCharacters($style['style_cate_id'] ?? "");
                $style_name = $styleCate[0][0] ?? "待定";
            }
            $product_type_id = $form->formatValue($style['product_type_id'] ?? 0, 0);
            if (empty($product_type_id)) {
                $flag = false;
                $error[$i][] = "产品线不能为空";
            } elseif (!is_numeric($product_type_id)) {
                $flag = false;
                $error[$i][] = "产品线填写有误";
            }
            $style_channel_id = $form->formatValue($style['style_channel_id'] ?? 0, 0);
            if (empty($style_channel_id)) {
                $style_channel_id = $styleAttr['style_channel_id'] ?? "";
                if (empty($style_channel_id) && !empty($style_sn)) {
                    $flag = false;
                    $error[$i][] = "款号填写有误，无法获取款式渠道";
                }
//                $flag = false;
//                $error[$i][] = "归属渠道不能为空";
            } elseif (!is_numeric($style_channel_id)) {
                $flag = false;
                $error[$i][] = "归属渠道填写有误";
            }
            $style_source_id = $form->formatValue($style['style_source_id'] ?? 0, 0);
            $style_material = $form->formatValue($style['style_material'] ?? "", "");
            if ($style_material === "") {
                $style_material = $styleAttr['style_material'] ?? "";
                if ($style_material === "" && !empty($style_sn)) {
                    $flag = false;
                    $error[$i][] = "款号填写有误，无法获取款式材质";
                }
//                $flag = false;
//                $error[$i][] = "款式材质不能为空";
            } elseif (!is_numeric($style_material)) {
                $flag = false;
                $error[$i][] = "款式材质填写有误";
            }
            $style_sex = $form->formatValue($style['style_sex'] ?? "", 0);
            if (empty($style_sex)) {
                $style_sex = $styleAttr['style_sex'] ?? "";
                if (empty($style_sex) && !empty($style_sn)) {
                    $flag = false;
                    $error[$i][] = "款号填写有误，无法获取款式性别";
                }
//                $flag = false;
//                $error[$i][] = "款式性别不能为空";
            } elseif (!is_numeric($style_sex)) {
                $flag = false;
                $error[$i][] = "款式性别填写有误";
            }
            $suttle_weight = $form->formatValue($style['suttle_weight'] ?? 0, 0);
            if (!empty($suttle_weight) && !is_numeric($suttle_weight)) {
                $flag = false;
                $error[$i][] = "连石重填写有误";
            }
            $inlay_craft = $form->formatMultipleValue($style['inlay_craft'] ?? "", "");
            if (!empty($style['inlay_craft']) && empty($inlay_craft)) {
                $flag = false;
                $error[$i][] = "镶嵌工艺填写有误";
            }
            $product_craft = $form->formatValue($style['product_craft'] ?? "", "");
            if (!empty($style['product_craft']) && empty($product_craft)) {
                $flag = false;
                $error[$i][] = "生产工艺填写有误";
            }
            $is_made = $form->formatValue($style['is_made'] ?? "", 1);
            if (!is_numeric($is_made)) {
                $flag = false;
                $error[$i][] = "是否支持定制填写有误";
            }
            //$is_gift = $form->formatValue($style['is_gift'], 0);
            $status = $form->formatValue($style['status'] ?? "", 0);
            if (!is_numeric($status)) {
                $flag = false;
                $error[$i][] = "是否启用填写有误";
            }
            $remark = $form->formatValue($style['remark'] ?? "", "");

            $factory_name1 = $form->formatValue($style['factory_id1'] ?? 0, 0);
            $factory_id1 = $factory_name1;
            if (!is_numeric($factory_id1)) {
                $flag = false;
                $error[$i][] = "工厂1名称填写有误";
            }
            $factory_mo1 = $form->formatValue($style['factory_mo1'] ?? "", "");
            $factory_remark1 = $form->formatValue($style['factory_remark1'] ?? "", "");
            $shipping_time1 = $form->formatValue($style['shipping_time1'] ?? "", "");
            $factory_made1 = $form->formatValue($style['factory_made1'] ?? "", 1);
            if (!is_numeric($factory_made1)) {
                $flag = false;
                $error[$i][] = "工厂1是否支持定制填写有误";
            }
            $factory_status1 = $form->formatValue($style['factory_status2'] ?? "", 1);
            if (!is_numeric($factory_status1)) {
                $flag = false;
                $error[$i][] = "工厂1是否启用填写有误";
            }

            $factory_name2 = $form->formatValue($style['factory_id2'] ?? 0, 0);
            $factory_id2 = $factory_name2;
            if (!is_numeric($factory_id2)) {
                $flag = false;
                $error[$i][] = "工厂2名称填写有误";
            }
            $factory_mo2 = $form->formatValue($style['factory_mo2'] ?? "", "");
            $factory_remark2 = $form->formatValue($style['factory_remark2'] ?? "", "");
            $shipping_time2 = $form->formatValue($style['shipping_time2'] ?? "", "");
            $factory_made2 = $form->formatValue($style['factory_made2'] ?? "", 1);
            if (!is_numeric($factory_made2)) {
                $flag = false;
                $error[$i][] = "工厂2是否支持定制填写有误";
            }
            $factory_status2 = $form->formatValue($style['factory_status2'] ?? "", 1);
            if (!is_numeric($factory_status2)) {
                $flag = false;
                $error[$i][] = "工厂2是否启用填写有误";
            }

            $peishi_fee = $form->formatValue($style['peishi_fee'] ?? "", '0.00');
            $peijian_fee = $form->formatValue($style['peijian_fee'] ?? "", '0.00');
            $gram_fee = $form->formatValue($style['gram_fee'] ?? "", '0.00');
            $basic_fee = $form->formatValue($style['basic_fee'] ?? "", '0.00');
            $xiangshi_fee = $form->formatValue($style['xiangshi_fee'] ?? "", '0.00');
            $fense_fee = $form->formatValue($style['fense_fee'] ?? "", '0.00');
            $pensa_fee = $form->formatValue($style['pensa_fee'] ?? "", '0.00');
            $lasa_fee = $form->formatValue($style['lasa_fee'] ?? "", '0.00');
            $bukou_fee = $form->formatValue($style['bukou_fee'] ?? "", '0.00');
            $templet_fee = $form->formatValue($style['templet_fee'] ?? "", '0.00');
            $cert_fee = $form->formatValue($style['cert_fee'] ?? "", '0.00');
            $technology_fee = $form->formatValue($style['technology_fee'] ?? "", '0.00');
            $chehuapian_fee = $form->formatValue($style['chehuapian_fee'] ?? "", '0.00');
            $fenjian_fee = $form->formatValue($style['fenjian_fee'] ?? "", '0.00');
            $luzhubian_fee = $form->formatValue($style['luzhubian_fee'] ?? "", '0.00');
            $other_fee = $form->formatValue($style['other_fee'] ?? "", '0.00');

            //$creator_id = \Yii::$app->user->identity->getId();
            //款式信息
            $styleList[] = $styleInfo = [
                'style_sn' => $style_sn,
                'style_name' => $style_name,
                'style_cate_id' => $style_cate_id,
                'product_type_id' => $product_type_id,
                'style_source_id' => $style_source_id,
                'style_channel_id' => $style_channel_id,
                'style_sex' => $style_sex,
                'style_material' => $style_material,
                'is_autosn' => $is_autosn,
                'is_made' => $is_made,
                //'is_gift' => $is_gift,
                'status' => $status,
                'remark' => $remark,
                'creator_id' => $creator_id,
                'created_at' => time(),
            ];

            //属性信息
            $attrList[] = [
                AttrIdEnum::SUTTLE_WEIGHT => $suttle_weight,
                AttrIdEnum::MATERIAL_TYPE => $this->getMaterialTypeValues($style_material),
                AttrIdEnum::XIANGQIAN_CRAFT => $inlay_craft,
                AttrIdEnum::PRODUCT_CRAFT => $product_craft,
            ];

            //工厂1信息
            $factoryList1[] = $factoryInfo1 = [
                'factory_id' => $factory_id1,
                'factory_mo' => $factory_mo1,
                'shipping_time' => $shipping_time1,
                'is_made' => $factory_made1,
                'is_default' => ConfirmEnum::YES,
                'remark' => $factory_remark1,
                'status' => $factory_status1,
                'creator_id' => $creator_id,
                'created_at' => time(),
            ];

            //工厂2信息
            $factoryList2[] = $factoryInfo2 = [
                'factory_id' => $factory_id2,
                'factory_mo' => $factory_mo2,
                'shipping_time' => $shipping_time2,
                'is_made' => $factory_made2,
                'is_default' => ConfirmEnum::NO,
                'remark' => $factory_remark2,
                'status' => $factory_status2,
                'creator_id' => $creator_id,
                'created_at' => time(),
            ];
            //工费信息
            $styleFee[] = $feeInfo = [
                'peishi_fee' => $peishi_fee,
                'peijian_fee' => $peijian_fee,
                'gram_fee' => $gram_fee,
                'basic_fee' => $basic_fee,
                'xiangshi_fee' => $xiangshi_fee,
                'technology_fee' => $technology_fee,
                'fense_fee' => $fense_fee,
                'pensa_fee' => $pensa_fee,
                'lasa_fee' => $lasa_fee,
                'chehuapian_fee' => $chehuapian_fee,
                'fenjian_fee' => $fenjian_fee,
                'luzhubian_fee' => $luzhubian_fee,
                'bukou_fee' => $bukou_fee,
                'templet_fee' => $templet_fee,
                'cert_fee' => $cert_fee,
                'other_fee' => $other_fee,
            ];

            $styleM = new StyleForm();
            $styleM->id = rand(1000000000, 9999999999);
            $styleM->setAttributes($styleInfo);
            if (!$styleM->validate()) {
                $flag = false;
                $error[$i][] = $this->getError($styleM);
            }

            $factoryM1 = new StyleFactory();
            $factoryM1->style_id = $styleM->id;
            if (!empty($factoryInfo1['factory_id'])) {
                $factoryM1->setAttributes($factoryInfo1);
                if (!$factoryM1->validate()) {
                    $flag = false;
                    $error[$i][] = $this->getError($factoryM1);
                }
            }
            if (!empty($factoryInfo2['factory_id'])) {
                $factoryM1->setAttributes($factoryInfo2);
                if (!$factoryM1->validate()) {
                    $flag = false;
                    $error[$i][] = $this->getError($factoryM1);
                }
            }
            foreach ($feeInfo as $type => $fee) {
                if (!empty($fee) && !is_numeric($fee)) {
                    $flag = false;
                    $error[$i][] = $form->getFeeTypeNameMap($type) . "必须为数字";
                }
            }
            $i++;
        }

        if (!$flag) {
            //发生错误
            $message = "";
            foreach ($error as $k => $v) {
                $s = "【" . implode('】,【', $v) . '】';
                $message .= '第' . ($k + 1) . '行：' . $s . '<hr>';
            }
            if ($error_off && count($error) > 0 && $message) {
                header("Content-Disposition: attachment;filename=错误提示" . date('YmdHis') . ".log");
                echo iconv("utf-8", "gbk", str_replace("<hr>", "\r\n", $message));
                exit();
            }
            throw new \Exception($message);
        }

        if (empty($styleList)) {
            throw new \Exception("导入数据不能为空");
        }
        $style_ids = $saveStone = $saveFactory = $saveFee = $saveAttr = [];
        foreach ($styleList as $k => $item) {
            //创建款式信息
            $styleM = new StyleForm();
            $styleM->id = null;
            $styleM->setAttributes($item);
            if ($styleM->status == StatusEnum::ENABLED) {//启用即审核
                $styleM->audit_status = AuditStatusEnum::PASS;
                $styleM->auditor_id = $creator_id;
                $styleM->audit_time = time();
                $styleM->audit_remark = "批量导入系统自动审核";
            } else {
                $styleM->audit_status = AuditStatusEnum::PENDING;
            }
//            if (empty($styleM->style_sn)) {
//                $styleM->is_autosn = AutoSnEnum::YES;
//            }
            if ($styleM->type) {
                $styleM->is_inlay = $styleM->type->is_inlay;
            }
            if (false === $styleM->save()) {
                throw new \Exception($this->getError($styleM));
            }
            $style_ids[] = $styleM->id;
            if (empty($styleM->style_sn)) {//款号为空自动创建
                Yii::$app->styleService->style->createStyleSn($styleM);
            }
            //创建审批流程
            if ($styleM->status != StatusEnum::ENABLED) {//未启用走审批流程
                Yii::$app->services->flowType->createFlow($this->targetType, $styleM->id, $styleM->style_sn);
            }
            //款式属性值
            foreach ($attrList[$k] as $attrId => $val) {
                if (empty($val)) {
                    continue;
                }
                $saveAttr[$styleM->id][$attrId] = $val;
            }
            //石头信息
            if($styleM->is_inlay){//产品线为镶嵌类
                $saveStone[] = [
                    'style_id' => $styleM->id,
                    'position' => StonePositionEnum::MAIN_STONE,
                    'stone_type' => $styleM->getStoneTypeByProduct($styleM),
                    'creator_id' => \Yii::$app->user->identity->getId(),
                    'created_at' => time(),
                ];
            }
            //款式工厂信息
            if (isset($factoryList1[$k]) || isset($factoryList2[$k])) {
                if (isset($factoryList1[$k]['factory_id'])
                    && !empty($factoryList1[$k]['factory_id'])) {
                    $factoryList1[$k]['style_id'] = $styleM->id;
                    $saveFactory[] = $factoryList1[$k];
                }
                if (isset($factoryList2[$k]['factory_id'])
                    && !empty($factoryList2[$k]['factory_id'])) {
                    $factoryList2[$k]['style_id'] = $styleM->id;
                    $saveFactory[] = $factoryList2[$k];

                }
            }
            //创建款式工费信息
            foreach ($styleFee[$k] as $type => $fee) {
                $fee_type = $form->getFeeTypeMap($type);
                if ($fee > 0 && !empty($fee) && !empty($fee_type)) {
                    $feeData = [];
                    $feeData['style_id'] = $styleM->id;
                    $feeData['fee_type'] = $fee_type;
                    $feeData['fee_price'] = sprintf("%.2f", round($fee, 2));
                    $feeData['creator_id'] = $creator_id;
                    $feeData['created_at'] = time();
                    $saveFee[] = $feeData;
                }
            }
//            $command = \Yii::$app->db->createCommand("call sp_create_style_attributes(" . $styleM->id . ");");
//            $command->execute();
        }
        //创建石头信息
        if(!empty($saveStone)){
            $value = [];
            $key = array_keys($saveStone[0]);
            foreach ($saveStone as $item) {
                $stoneM = new StyleStone();
                $stoneM->setAttributes($item);
                if (!$stoneM->validate()) {
                    throw new \Exception($this->getError($stoneM));
                }
                $value[] = array_values($item);
                if (count($value) >= 10) {
                    $res = \Yii::$app->db->createCommand()->batchInsert(StyleStone::tableName(), $key, $value)->execute();
                    if (false === $res) {
                        throw new \Exception("创建石头信息失败1");
                    }
                    $value = [];
                }
            }
            if (!empty($value)) {
                $res = \Yii::$app->db->createCommand()->batchInsert(StyleStone::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建石头信息失败2");
                }
            }
        }
        //创建款式工厂信息
        if (!empty($saveFactory)) {
            $value = [];
            $key = array_keys($saveFactory[0]);
            foreach ($saveFactory as $item) {
                $factoryM = new StyleFactory();
                $factoryM->setAttributes($item);
                if (!$factoryM->validate()) {
                    throw new \Exception($this->getError($factoryM));
                }
                $value[] = array_values($item);
                if (count($value) >= 10) {
                    $res = \Yii::$app->db->createCommand()->batchInsert(StyleFactory::tableName(), $key, $value)->execute();
                    if (false === $res) {
                        throw new \Exception("创建工厂信息失败1");
                    }
                    $value = [];
                }
            }
            if (!empty($value)) {
                $res = \Yii::$app->db->createCommand()->batchInsert(StyleFactory::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建工厂信息失败2");
                }
            }
        }
        //创建款式工费信息
        if (!empty($saveFee)) {
            $value = [];
            $key = array_keys($saveFee[0]);
            foreach ($saveFee as $item) {
                $feeM = new StyleFactoryFee();
                $feeM->setAttributes($item);
                if (!$feeM->validate()) {
                    throw new \Exception($this->getError($feeM));
                }
                $value[] = array_values($item);
                if (count($value) >= 10) {
                    $res = \Yii::$app->db->createCommand()->batchInsert(StyleFactoryFee::tableName(), $key, $value)->execute();
                    if (false === $res) {
                        throw new \Exception("创建工费信息失败1");
                    }
                    $value = [];
                }
            }
            if (!empty($value)) {
                $res = \Yii::$app->db->createCommand()->batchInsert(StyleFactoryFee::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建工费信息失败2");
                }
            }
        }
        //创建款式属性信息(属性值)
        if (!empty($style_ids)) {
            $styleIds = [];
            foreach ($style_ids as $style_id) {
                $styleIds[] = $style_id;
                if (count($styleIds) >= 1000) {
                    $command = \Yii::$app->db->createCommand("call sp_create_style_attributes('" . implode(',', $styleIds) . "');");
                    $command->execute();
                    $styleIds = [];
                }
            }
            if (!empty($styleIds)) {
                $command = \Yii::$app->db->createCommand("call sp_create_style_attributes('" . implode(',', $styleIds) . "');");
                $command->execute();
            }
        }
        //款式属性信息
        if(!empty($saveAttr)){
            foreach ($saveAttr as $style_id => $attrInfo) {
                $styleM = Style::find()->select(['style_cate_id'])->where(['id' => $style_id])->one();
                foreach ($attrInfo as $attrId => $val) {
                    $attr = \Yii::$app->styleService->attribute->getSpecAttrList($attrId, $styleM->style_cate_id);
                    if (!empty($attr)) {
                        $attr = $attr[0] ?? [];
                        $attr_list = [
                            'style_id' => $style_id,
                            'attr_id' => $attrId,
                            'input_type' => $attr['input_type'] ?? 0,
                            'is_require' => $attr['is_require'] ?? 0,
                            'attr_type' => $attr['attr_type'] ?? 0,
                            'is_inlay' => $attr['is_inlay'] ?? 0,
                            'sort' => $attr['sort'] ?? 0,
                            'attr_values' => (string)$val,
                        ];
                        $styleAttr = StyleAttribute::find()->where(['style_id' => $style_id, 'attr_id' => $attrId])->one();
                        $styleAttr = $styleAttr ?? new StyleAttribute();
                        $styleAttr->attributes = $attr_list;
                        if (false === $styleAttr->save()) {
                            throw new \Exception($this->getError($styleAttr));
                        }
                    } else {
                        continue;
                    }
                }
            }
        }
    }

    /**
     * 根据款号拆分款式信息
     * @param string $style_sn
     * @param bool $save
     * @return array
     * @throws
     */
    public function extendAttrByStyleSn($style_sn = null, $save = true)
    {
        $model = new StyleForm();
        $style_material = "";//款式材质
        $style_cate_id = $style_sex = $style_channel_id = 0;
        if (!empty($style_sn)) {
            $styleArr = str_split(strtoupper($style_sn));
            //1.产品分类,//2.款式性别
            $cateCode = $styleArr[1] ?? "";
            list($style_cate_id, $style_sex) = $model->getCateCodeId($cateCode);
            //3.款式渠道
            $channelCode = $styleArr[0] ?? "";
            $style_channel_id = $model->getChannelCodeId($channelCode);;
            //4.款式材质
            $material_id = $styleArr[8] ?? "";
            $materialArr = StyleMaterialEnum::getMap();
            if (!in_array($material_id, array_keys($materialArr))) {
                $style_material = "";
            } else {
                $style_material = $material_id;
            }
            //$style_material = $materialArr[$material_id] ?? "";
        }
        return [
            'style_cate_id' => $style_cate_id,
            'style_sex' => $style_sex,
            'style_channel_id' => $style_channel_id,
            'style_material' => $style_material,
        ];
    }

    /**
     * 获取材质属性信息
     * @param int $style_material
     * @return string
     * @throws
     */
    public function getMaterialTypeValues($style_material)
    {
        switch ($style_material) {
            case StyleMaterialEnum::GOLD:
                $materialType = [
                    AttrValueIdEnum::K1,
                    AttrValueIdEnum::K9,
                    AttrValueIdEnum::K10,
                    AttrValueIdEnum::K14,
                    AttrValueIdEnum::K18,
                    AttrValueIdEnum::AU990,
                    AttrValueIdEnum::AU999,
                    AttrValueIdEnum::AU9999,
                    AttrValueIdEnum::PT950,
                    AttrValueIdEnum::PT900,
                    AttrValueIdEnum::PT990,
                    AttrValueIdEnum::PD,
                ];
                break;
            case StyleMaterialEnum::SILVER:
                $materialType = [
                    AttrValueIdEnum::AG925,
                    AttrValueIdEnum::AG999,
                ];
                break;
            case StyleMaterialEnum::COPPER:
                $materialType = [
                    AttrValueIdEnum::COPPER,
                ];
                break;
            case StyleMaterialEnum::ALLOY:
                $materialType = [
                    AttrValueIdEnum::ALLOY,
                    AttrValueIdEnum::STEEL,
                ];
                break;
            case StyleMaterialEnum::OTHER:
                $materialType = [];
                break;
            default:
                $materialType = [];
        }
        return implode(",", $materialType) ?? "";
    }

}