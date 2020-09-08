<?php

namespace addons\Style\common\forms;

use Yii;
use addons\Style\common\models\Style;
use addons\Style\common\enums\FactoryFeeEnum;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;

/**
 * 款式 Form
 *
 */
class StyleForm extends Style
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => ['csv']],//'skipOnEmpty' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels(), [
            'file' => '文件上传',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleList()
    {
        $values = [
            '#', '#',
            $this->getAttributeLabel('style_cate_id') . $this->formatTitleId($this->getCateList()),
            $this->getAttributeLabel('product_type_id') . $this->formatTitleId($this->getProductList()),
            $this->getAttributeLabel('style_channel_id') . $this->formatTitleId($this->getChannelList()),
            $this->getAttributeLabel('style_source_id') . $this->formatTitleId($this->getSourceList()),
            $this->getAttributeLabel('style_material') . $this->formatTitleId($this->getMaterialList()),
            $this->getAttributeLabel('style_sex') . $this->formatTitleId($this->getSexList()),
            $this->getAttributeLabel('is_made') . $this->formatTitleId($this->getIsMadeList()),
            $this->getAttributeLabel('is_gift') . $this->formatTitleId($this->getIsGiftList()),
            '#',

            "工厂名称1" . $this->formatTitleId($this->getSupplierList()),
            '#', '#', '#',
            "是否支持定制1" . $this->formatTitleId($this->getIsMadeList()),

            "工厂名称2" . $this->formatTitleId($this->getSupplierList()),
            '#', '#', '#',
            "是否支持定制2" . $this->formatTitleId($this->getIsMadeList()),

            '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#',
        ];
        $fields = [
            '(*)款式名称[style_name]', '款式编号[style_sn]', '(*)款式分类[style_cate_id]', '(*)产品线[product_type_id]',
            '(*)归属渠道[style_channel_id]', '款式来源[style_source_id]', '(*)款式材质[style_material]', '(*)款式性别[style_sex]',
            '是否支持定制[is_made]', '是否赠品[is_gift]', '备注[remark]',

            '工厂名称1(默认工厂)[factory_id1]', '工厂模号1[factory_mo1]', '备注(计费方式)1[factory_remark1]', '出货时间(天)1[shipping_time1]', '是否支持定制1[factory_made1]',
            '工厂名称2[factory_id2]', '工厂模号2[factory_mo2]', '备注(计费方式)2[factory_remark2]', '出货时间(天)2[shipping_time2]', '是否支持定制2[factory_made2]',

            '配石工费/ct[peishi_fee]', '配件工费[peijian_fee]', '克/工费[gram_fee]', '基本工费[basic_fee]', '镶石费[xiangshi_fee]','表面工艺费[technology_fee]',
            '分色费[fense_fee]', '喷拉沙费[penlasa_fee]', '补口费[bukou_fee]', '版费[templet_fee]', '证书费[cert_fee]', '其他费用[other_fee]',
        ];
        return [$values, $fields];
    }

    /**
     * {@inheritdoc}
     */
    public function trimField($data, $field)
    {
        $res = [];
        foreach ($data as $k => $v) {
            $str = StringHelper::strIconv($v);
            $name = $field[$k] ?? "";
            if(!empty($name)){
                $res[$name] = $str;
            }else{
                return false;
            }
        }
        return $res ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function formatField($title)
    {
        $field = [];
        foreach ($title as $k => $v) {
            $str = StringHelper::strIconv($v);
            preg_match_all("/(?:\[)(.*)(?:\])/i", $str, $result);
            if (isset($result[1][0]) && !empty($result[1][0])) {
                $field[] = $result[1][0] ?? "";
            }else{
                return false;
            }
        }
        return $field ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function formatValue($value = null, $defaultValue = null)
    {
        if (!empty($value)) {
            $result = array();
            preg_match_all("/(?:\[)(.*)(?:\])/i", $value, $result);
            if (isset($result[1][0])) {
                if ($result[1][0] === 0) {
                    return $defaultValue;
                } elseif (!empty($result[1][0])) {
                    return $result[1][0];
                } else {
                    return $defaultValue;
                }
            } else {
                return $value;
            }
        } else {
            return $defaultValue;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function formatTitle($values)
    {
        $title = "";
        if (!empty($values)) {
            $title = "[" . implode('|', $values) . "]";
        }
        return $title ?? "";
    }

    /**
     * {@inheritdoc}
     */
    public function formatTitleId($data)
    {
        $title = "[";
        if (!empty($data)) {
            foreach ($data as $id => $value) {
                $title .= $value . "[" . $id . "]|";
            }
        }
        return rtrim($title, "|") . "]" ?? "";
    }

    /**
     * 款式分类列表
     * @return array
     */
    public function getCateList()
    {
        return \Yii::$app->styleService->styleCate::getList() ?? [];
    }

    /**
     * 产品线列表
     * @return array
     */
    public function getProductList()
    {
        return \Yii::$app->styleService->productType::getList() ?? [];
    }

    /**
     * 归属渠道
     * @return array
     */
    public function getChannelList()
    {
        return \Yii::$app->styleService->styleChannel->getDropDown() ?? [];
    }

    /**
     * 款式来源
     * @return array
     */
    public function getSourceList()
    {
        return \Yii::$app->styleService->styleSource->getDropDown() ?? [];
    }

    /**
     * 款式材质
     * @return array
     */
    public function getMaterialList()
    {
        return \addons\Style\common\enums\StyleMaterialEnum::getMap() ?? [];
    }

    /**
     * 款式性别
     * @return array
     */
    public function getSexList()
    {
        return \addons\Style\common\enums\StyleSexEnum::getMap() ?? [];
    }

    /**
     * 是否定制
     * @return array
     */
    public function getIsMadeList()
    {
        return \common\enums\ConfirmEnum::getMap() ?? [];
    }

    /**
     * 是否赠品
     * @return array
     */
    public function getIsGiftList()
    {
        return \common\enums\ConfirmEnum::getMap() ?? [];
    }

    /**
     * 供应商
     * @return array
     */
    public function getSupplierList()
    {
        return Yii::$app->supplyService->supplier->getDropDown() ?? [];
    }

    /**
     * 工费类型映射
     * @param string $type
     * @return string
     */
    public function getFeeTypeMap($type)
    {
        $feeType = [
            'peishi_fee' => FactoryFeeEnum::PEISHI_GF,
            'peijian_fee' => FactoryFeeEnum::PARTS_GF,
            'gram_fee' => FactoryFeeEnum::GEAM_GF,
            'basic_fee' => FactoryFeeEnum::BASIC_GF,
            'xiangshi_fee' => FactoryFeeEnum::INLAID_GF,
            'technology_fee' => FactoryFeeEnum::TECHNOLOGY_GF,
            'fense_fee' => FactoryFeeEnum::FENSE_GF,
            'penlasa_fee' => FactoryFeeEnum::PENLASHA_GF,
            'bukou_fee' => FactoryFeeEnum::BUKOU_GF,
            'templet_fee' => FactoryFeeEnum::TEMPLET_GF,
            'cert_fee' => FactoryFeeEnum::CERT_GF,
            'other_fee' => FactoryFeeEnum::OTHER_GF,
        ];
        return $feeType[$type] ?? "";
    }
}
