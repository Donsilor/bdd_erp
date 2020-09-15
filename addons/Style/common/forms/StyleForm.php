<?php

namespace addons\Style\common\forms;

use Yii;
use addons\Style\common\models\Style;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\FactoryFeeEnum;
use addons\Style\common\enums\StyleCateEnum;
use addons\Style\common\enums\StyleChannelEnum;
use addons\Style\common\enums\StyleSexEnum;
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
        $cate = $this->getCateList();
        //unset($cate[3], $cate[13], $cate[14], $cate[15], $cate[16], $cate[17]);
        $unCate = [3, 13, 14, 15, 16, 17];
        if (!empty($cate)) {
            foreach ($unCate as $id) {
                if (isset($cate[$id])) {
                    unset($cate[$id]);
                }
            }
        }
        $product = $this->getProductList();
        //unset($product[1], $product[3], $product[4]);
        $unProduct = [1, 3, 4];
        if (!empty($product)) {
            foreach ($unProduct as $id) {
                if (isset($product[$id])) {
                    unset($product[$id]);
                }
            }
        }
        $values = [
            '#', '#',
            $this->formatTitleId($cate),
            $this->formatTitleId($product),
            $this->formatTitleId($this->getStatusList()),
            $this->formatTitleId($this->getChannelList()),
            $this->formatTitleId($this->getSourceList()),
            $this->formatTitleId($this->getMaterialList()),
            $this->formatTitleId($this->getSexList()),
            '#',
            $this->formatTitleId($this->getInlayCraftMap(), "|"),
            $this->formatTitleId($this->getProductCraftMap()),
            $this->formatTitleId($this->getIsMadeList()),
            //$this->getAttributeLabel('is_gift') . $this->formatTitleId($this->getIsGiftList()),
            '#',

            $this->formatTitleId($this->getSupplierList()),
            '#', '#', '#',
            $this->formatTitleId($this->getIsMadeList()),
            $this->formatTitleId($this->getStatusList()),

            $this->formatTitleId($this->getSupplierList()),
            '#', '#', '#',
            $this->formatTitleId($this->getIsMadeList()),
            $this->formatTitleId($this->getStatusList()),

            '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#',
        ];
        $fields = [
            '款式名称[style_name]', '(*)款式编号[style_sn]', '款式分类[style_cate_id]', '(*)产品线[product_type_id]', '是否启用[status]',
            '归属渠道[style_channel_id]', '款式来源[style_source_id]', '款式材质[style_material]', '款式性别[style_sex]', '连石重(g)[suttle_weight]', '镶嵌工艺(多个用“|”分割)[inlay_craft]', '生产工艺[product_craft]',
            '是否支持定制[is_made]', '备注[remark]',

            '工厂名称1(默认工厂)[factory_id1]', '工厂模号1[factory_mo1]', '备注(计费方式)1[factory_remark1]', '出货时间(天)1[shipping_time1]', '是否支持定制1[factory_made1]', '是否启用1[factory_status1]',
            '工厂名称2[factory_id2]', '工厂模号2[factory_mo2]', '备注(计费方式)2[factory_remark2]', '出货时间(天)2[shipping_time2]', '是否支持定制2[factory_made2]', '是否启用2[factory_status2]',

            '配石工费/ct[peishi_fee]', '配件工费[peijian_fee]', '克/工费[gram_fee]', '基本工费[basic_fee]', '镶石费/颗[xiangshi_fee]', '表面工艺费[technology_fee]',
            '分色费[fense_fee]', '喷沙费[pensa_fee]', '拉沙费[lasa_fee]', '车花片[chehuapian_fee]', '分件费[fenjian_fee]', '辘珠边[luzhubian_fee]', '补口费[bukou_fee]', '版费[templet_fee]', '证书费[cert_fee]', '其他费用[other_fee]',
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
            if (!empty($name)) {
                if (!empty($str)) {
                    $str = str_replace(',', '，', $str);
                    $str = str_replace('】', '', $str);
                }
                $res[$name] = $str;
            } else {
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
            } else {
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
     * 多选
     * {@inheritdoc}
     */
    public function formatMultipleValue($value = null, $defaultValue = null)
    {
        if (!empty($value)) {
            $arr = StringHelper::explode($value, "|");
            $arr = array_unique(array_filter($arr));
            $values = "";
            foreach ($arr as $item) {
                $result = array();
                preg_match_all("/(?:\[)(.*)(?:\])/i", $item, $result);
                if (isset($result[1][0])) {
                    if ($result[1][0] === 0) {
                        return $defaultValue;
                    } elseif (!empty($result[1][0])) {
                        $values .= $result[1][0] . ",";
                    } else {
                        return $defaultValue;
                    }
                } else {
                    return $item;
                }
            }
            return rtrim($values, ",") ?? "";
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
    public function formatTitleId($data, $str = "")
    {
        $title = "";
        if (!empty($data)) {
            foreach ($data as $id => $value) {
                $title .= $value . "[" . $id . "]{$str}】";
            }
        }
        return rtrim($title, "]") ?? "";
    }

    /**
     * {@inheritdoc}
     */
    public function getCateCodeId($cateCode)
    {
        $cate_type_id = 0;
        $style_sex = 0;
        $codeInfo = StyleCateEnum::getCodeMap();
        foreach ($codeInfo as $id => $code) {
            if (array_search($cateCode, $code) !== false) {
                $sex = array_search($cateCode, $code);
                $cate_type_id = $id;
                switch ($sex) {
                    case StyleSexEnum::MAN:
                        $style_sex = StyleSexEnum::MAN;
                        break;
                    case StyleSexEnum::WOMEN:
                        $style_sex = StyleSexEnum::WOMEN;
                        break;
                    default:
                        $style_sex = StyleSexEnum::COMMON;
                }
                break;
            }
        }
        return [$cate_type_id, $style_sex];
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelCodeId($channelCode)
    {
        $codeInfo = array_flip(StyleChannelEnum::getCodeMap());
        return $codeInfo[$channelCode] ?? 0;
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
     * 是否启用
     * @return array
     */
    public function getStatusList()
    {
        return \common\enums\StatusEnum::getMap() ?? [];
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
     * 镶嵌工艺
     * @return array
     */
    public function getInlayCraftMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::XIANGQIAN_CRAFT) ?? [];
    }

    /**
     * 生产工艺
     * @return array
     */
    public function getProductCraftMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::PRODUCT_CRAFT) ?? [];
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
            'fense_fee' => FactoryFeeEnum::FENSE_GF,
            'pensa_fee' => FactoryFeeEnum::PENSHA_GF,
            'lasa_fee' => FactoryFeeEnum::LASHA_GF,
            'chehuapian_fee' => FactoryFeeEnum::CHEHUAPIAN_GF,
            'fenjian_fee' => FactoryFeeEnum::FENJIAN_GF,
            'luzhubian_fee' => FactoryFeeEnum::LUZHUBIAN_GF,
            'bukou_fee' => FactoryFeeEnum::BUKOU_GF,
            'templet_fee' => FactoryFeeEnum::TEMPLET_GF,
            'cert_fee' => FactoryFeeEnum::CERT_GF,
            'technology_fee' => FactoryFeeEnum::TECHNOLOGY_GF,
            'other_fee' => FactoryFeeEnum::OTHER_GF,
        ];
        return $feeType[$type] ?? "";
    }

    /**
     * 工费类型映射
     * @param string $type
     * @return string
     */
    public function getFeeTypeNameMap($type)
    {
        $feeName = [
            'peishi_fee' => "配石费",
            'peijian_fee' => "配件费",
            'gram_fee' => "克/工费",
            'basic_fee' => "基本工费",
            'xiangshi_fee' => "镶石费",
            'fense_fee' => "分色费",
            'pensa_fee' => "喷沙费",
            'lasa_fee' => "拉沙费",
            'chehuapian_fee' => "车花片",
            'fenjian_fee' => "分件费",
            'luzhubian_fee' => "辘珠边",
            'bukou_fee' => "补口费",
            'templet_fee' => "版费",
            'cert_fee' => "证书费",
            'technology_fee' => "表面工艺费",
            'other_fee' => "其他费用",
        ];
        return $feeName[$type] ?? "";
    }
}
