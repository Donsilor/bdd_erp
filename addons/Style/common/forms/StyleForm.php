<?php

namespace addons\Style\common\forms;

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
    public function getTitleList()
    {
        $values = [
            '#',
            '#',

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
     * 款式分类列表
     * @return array
     */
    public function getCateMap()
    {
        return \Yii::$app->styleService->styleCate::getDropDown() ?? [];
    }

    /**
     * 产品线列表
     * @return array
     */
    public function getProductMap()
    {
        return \Yii::$app->styleService->productType::getDropDown() ?? [];
    }
}
