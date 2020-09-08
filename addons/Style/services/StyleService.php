<?php

namespace addons\Style\services;

use addons\Style\common\enums\InlayEnum;
use common\enums\AuditStatusEnum;
use Yii;
use common\helpers\Url;
use common\components\Service;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleFactory;
use addons\Style\common\models\StyleFactoryFee;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\models\StyleImages;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\forms\StyleForm;
use addons\Style\common\models\StyleGift;
use common\helpers\UploadHelper;
use common\enums\ConfirmEnum;
use common\enums\StatusEnum;
use common\enums\AutoSnEnum;

/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleService extends Service
{

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
        $error = $field = $styleList = $factoryList1 = $factoryList2 = $styleFee = [];
        while ($style = fgetcsv($file)) {
            if (count($style) != 35) {
                throw new \Exception("模板格式不正确，请下载最新模板");
            }
            if ($i >= 102) {
                throw new \Exception("每次最多能导入100条数据");
            }
            if ($i <= 1) {
                if ($i == 1) {
                    $field = $form->formatField($style);
                    if ($field == false) {
                        throw new \Exception("表头格式不对[code=1]");
                    }
                    if (count($field) != 35) {
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
            $style_name = $form->formatValue($style['style_name'], "");
            if (empty($style_name)) {
                $flag = false;
                $error[$i][] = "款式名称不能为空";
            }
            $style_sn = $form->formatValue($style['style_sn'], "");
            $style_cate_id = $form->formatValue($style['style_cate_id'], 0);
            if (empty($style_cate_id)) {
                $flag = false;
                $error[$i][] = "款式分类不能为空";
            } elseif (!is_numeric($style_cate_id)) {
                $flag = false;
                $error[$i][] = "款式分类填写有误";
            }
            $product_type_id = $form->formatValue($style['product_type_id'], 0);
            if (empty($product_type_id)) {
                $flag = false;
                $error[$i][] = "产品线不能为空";
            } elseif (!is_numeric($product_type_id)) {
                $flag = false;
                $error[$i][] = "产品线填写有误";
            }
            $style_channel_id = $form->formatValue($style['style_channel_id'], 0);
            if (empty($style_channel_id)) {
                $flag = false;
                $error[$i][] = "归属渠道不能为空";
            } elseif (!is_numeric($style_channel_id)) {
                $flag = false;
                $error[$i][] = "归属渠道填写有误";
            }
            $style_source_id = $form->formatValue($style['style_source_id'], 0);
            $style_material = $form->formatValue($style['style_material'], 0);
            if (empty($style_material)) {
                $flag = false;
                $error[$i][] = "款式材质不能为空";
            } elseif (!is_numeric($style_material)) {
                $flag = false;
                $error[$i][] = "款式材质填写有误";
            }
            $style_sex = $form->formatValue($style['style_sex'], 0);
            if (empty($style_sex)) {
                $flag = false;
                $error[$i][] = "款式性别不能为空";
            } elseif (!is_numeric($style_sex)) {
                $flag = false;
                $error[$i][] = "款式性别填写有误";
            }
            $is_made = $form->formatValue($style['is_made'], 1);
            if (!is_numeric($is_made)) {
                $flag = false;
                $error[$i][] = "是否支持定制填写有误";
            }
            //$is_gift = $form->formatValue($style['is_gift'], 0);
            $status = $form->formatValue($style['status'], 1);
            if (!is_numeric($status)) {
                $flag = false;
                $error[$i][] = "是否启用填写有误";
            }
            $remark = $form->formatValue($style['remark'], "");

            $factory_name1 = $form->formatValue($style['factory_id1'], 0);
            $factory_id1 = $factory_name1;
            if (!is_numeric($factory_id1)) {
                $flag = false;
                $error[$i][] = "工厂1名称填写有误";
            }
            $factory_mo1 = $form->formatValue($style['factory_mo1'], "");
            $factory_remark1 = $form->formatValue($style['factory_remark1'], "");
            $shipping_time1 = $form->formatValue($style['shipping_time1'], "");
            $factory_made1 = $form->formatValue($style['factory_made1'], 1);
            if (!is_numeric($factory_made1)) {
                $flag = false;
                $error[$i][] = "工厂1是否支持定制填写有误";
            }
            $factory_status1 = $form->formatValue($style['factory_status2'], 1);
            if (!is_numeric($factory_status1)) {
                $flag = false;
                $error[$i][] = "工厂1是否启用填写有误";
            }

            $factory_name2 = $form->formatValue($style['factory_id2'], 0);
            $factory_id2 = $factory_name2;
            if (!is_numeric($factory_id2)) {
                $flag = false;
                $error[$i][] = "工厂2名称填写有误";
            }
            $factory_mo2 = $form->formatValue($style['factory_mo2'], "");
            $factory_remark2 = $form->formatValue($style['factory_remark2'], "");
            $shipping_time2 = $form->formatValue($style['shipping_time2'], "");
            $factory_made2 = $form->formatValue($style['factory_made2'], 1);
            if (!is_numeric($factory_made2)) {
                $flag = false;
                $error[$i][] = "工厂2是否支持定制填写有误";
            }
            $factory_status2 = $form->formatValue($style['factory_status2'], 1);
            if (!is_numeric($factory_status2)) {
                $flag = false;
                $error[$i][] = "工厂2是否启用填写有误";
            }

            $peishi_fee = $form->formatValue($style['peishi_fee'], '0.00');
            $peijian_fee = $form->formatValue($style['peijian_fee'], '0.00');
            $gram_fee = $form->formatValue($style['gram_fee'], '0.00');
            $basic_fee = $form->formatValue($style['basic_fee'], '0.00');
            $xiangshi_fee = $form->formatValue($style['xiangshi_fee'], '0.00');
            $technology_fee = $form->formatValue($style['technology_fee'], '0.00');
            $fense_fee = $form->formatValue($style['fense_fee'], '0.00');
            $penlasa_fee = $form->formatValue($style['penlasa_fee'], '0.00');
            $bukou_fee = $form->formatValue($style['bukou_fee'], '0.00');
            $templet_fee = $form->formatValue($style['templet_fee'], '0.00');
            $cert_fee = $form->formatValue($style['cert_fee'], '0.00');
            $other_fee = $form->formatValue($style['other_fee'], '0.00');

            $creator_id = \Yii::$app->user->identity->getId();
            $styleList[] = $styleInfo = [
                'style_sn' => $style_sn,
                'style_name' => $style_name,
                'style_cate_id' => $style_cate_id,
                'product_type_id' => $product_type_id,
                'style_source_id' => $style_source_id,
                'style_channel_id' => $style_channel_id,
                'style_sex' => $style_sex,
                'style_material' => $style_material,
                'is_autosn' => 1,
                'is_made' => $is_made,
                //'is_gift' => $is_gift,
                'status' => $status,
                'remark' => $remark,
                'creator_id' => $creator_id,
                'created_at' => time(),
            ];

            $factoryList1[] = $factoryInfo1 = [
                'factory_id' => $factory_id1,
                'factory_mo' => $factory_mo1,
                'shipping_time' => $shipping_time1,
                'is_made' => $factory_made1,
                'is_default' => 1,
                'remark' => $factory_remark1,
                'status' => $factory_status1,
                'creator_id' => $creator_id,
                'created_at' => time(),
            ];

            $factoryList2[] = $factoryInfo2 = [
                'factory_id' => $factory_id2,
                'factory_mo' => $factory_mo2,
                'shipping_time' => $shipping_time2,
                'is_made' => $factory_made2,
                'remark' => $factory_remark2,
                'status' => $factory_status2,
                'creator_id' => $creator_id,
                'created_at' => time(),
            ];

            $styleFee[] = $styleInfo = [
                'peishi_fee' => $peishi_fee,
                'peijian_fee' => $peijian_fee,
                'gram_fee' => $gram_fee,
                'basic_fee' => $basic_fee,
                'xiangshi_fee' => $xiangshi_fee,
                'technology_fee' => $technology_fee,
                'fense_fee' => $fense_fee,
                'penlasa_fee' => $penlasa_fee,
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
            foreach ($styleInfo as $item) {
                if (!empty($item) && !is_numeric($item)) {
                    $flag = false;
                    $error[$i][] = "费用必须为数字";
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
        $style_ids = [];
        foreach ($styleList as $k => $item) {
            //创建款式信息
            $styleM = new Style();
            $styleM->id = null;
            $styleM->setAttributes($item);
            if ($styleM->status == StatusEnum::ENABLED) {//启动即审核
                $styleM->audit_status = AuditStatusEnum::PASS;
                $styleM->auditor_id = \Yii::$app->user->identity->getId();
                $styleM->audit_time = time();
                $styleM->audit_remark = "批量导入";
            }
            if (empty($styleM->style_sn)) {
                $styleM->is_autosn = AutoSnEnum::YES;
            }
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
            //创建款式工厂信息
            if (isset($factoryList1[$k]) || isset($factoryList2[$k])) {
                if (isset($factoryList1[$k]['factory_id'])
                    && !empty($factoryList1[$k]['factory_id'])) {

                    $factoryM = new StyleFactory();
                    $factoryM->style_id = $styleM->id;
                    $factoryM->setAttributes($factoryList1[$k]);
                    if (false === $factoryM->save()) {
                        throw new \Exception($this->getError($factoryM));
                    }
                }
                if (isset($factoryList2[$k]['factory_id'])
                    && !empty($factoryList2[$k]['factory_id'])) {

                    $factoryM = new StyleFactory();
                    $factoryM->style_id = $styleM->id;
                    $factoryM->setAttributes($factoryList2[$k]);
                    if (false === $factoryM->save()) {
                        throw new \Exception($this->getError($factoryM));
                    }
                }
            }
            //创建款式工费信息
            $styleFee = $styleFee[$k] ?? [];
            foreach ($styleFee as $type => $fee) {
                $fee_type = $form->getFeeTypeMap($type);
                if ($fee > 0 && !empty($fee) && !empty($fee_type)) {
                    $feeM = new StyleFactoryFee();
                    $feeM->style_id = $styleM->id;
                    $feeM->fee_type = $fee_type;
                    $feeM->fee_price = sprintf("%.2f", round($fee, 2));
                    $feeM->creator_id = \Yii::$app->user->identity->getId();
                    $feeM->created_at = time();
                    $feeM->setAttributes($fee);
                    if (false === $feeM->save()) {
                        throw new \Exception($this->getError($feeM));
                    }
                }
            }

            $command = \Yii::$app->db->createCommand("call sp_create_style_attributes(" . $styleM->id . ");");
            $command->execute();
        }
        //创建款式属性信息
//        if (!empty($style_ids)) {
//            $command = \Yii::$app->db->createCommand("call sp_create_style_attributes(" . implode(',', $style_ids) . ");");
//            $command->execute();
//        }
    }
}