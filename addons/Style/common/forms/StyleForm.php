<?php

namespace addons\Style\common\forms;

use common\helpers\ArrayHelper;
use Yii;
use addons\Style\common\models\Style;

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
            '#','#',
            $this->formatTitleId($this->getCateList()),
            $this->formatTitleId($this->getProductList()),
            $this->formatTitleId($this->getChannelList()),
            $this->formatTitleId($this->getSourceList()),
            $this->formatTitleId($this->getMaterialList()),
            $this->formatTitleId($this->getSexList()),
            $this->formatTitleId($this->getIsMadeList()),
            $this->formatTitleId($this->getIsGiftList()),
            '#',

            $this->formatTitleId($this->getSupplierList()),
            '#','#','#',
            $this->formatTitleId($this->getIsMadeList()),

            $this->formatTitleId($this->getSupplierList()),
            '#','#','#',
            $this->formatTitleId($this->getIsMadeList()),

            '#','#','#','#','#','#','#','#','#','#',
        ];
        $fields = [
            '*款式名称', '款式编号', '款式分类', '产品线', '归属渠道', '款式来源', '*款式材质', '*款式性别', '是否支持定制', '是否赠品', '备注',
            '工厂名称1', '工厂模号1', '备注(计费方式)1', '出货时间(天)1', '是否支持定制1',
            '工厂名称2', '工厂模号2', '备注(计费方式)2', '出货时间(天)2', '是否支持定制2',
            '配石工费/ct', '配件工费', '克/工费', '基本工费', '镶石费表面工艺费', '分色费喷拉沙费', '补口费', '版费', '证书费', '其他费用',
        ];
        return [$values, $fields];
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
}
