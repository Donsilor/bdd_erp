<?php

use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;

$this->title = '新增货品';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'produce_sns')->textInput(["placeholder"=>"批量输入请使用逗号或空格或换行符隔开"]) ?>
                    </div>
                    <div class="col-lg-1">
                        <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:26px;','onclick'=>"searchReceiptGoods()"]) ?>
                    </div>
                </div>
                <span style="color:red;">提示：一次最多添加100件商品，可分多次添加，已添加数量：<?= $num;?></span>
                <div class="row">
                    <div class="box-body table-responsive">
                        <div class="tab-content">
                            <?= $form->field($model, 'goods')->widget(MultipleInput::className(),[
                                'max' => 99,
                                'addButtonOptions'=>['label'=>'','class'=>''],
                                'value' => $goods_list,
                                'columns' => [
                                    [
                                        'name' =>'purchase_sn',
                                        'title'=>$model->getAttributeLabel('purchase_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:160px'
                                        ]
                                    ],
                                    [
                                        'name' =>'produce_sn',
                                        'title'=>$modelG->getAttributeLabel('produce_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:160px'
                                        ]
                                    ],
                                    [
                                        'name' =>'order_sn',
                                        'title'=>$modelG->getAttributeLabel('order_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:160px'
                                        ]
                                    ],
                                    [
                                        'name' =>'goods_name',
                                        'title'=>$modelG->getAttributeLabel('goods_name'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:260px'
                                        ]
                                    ],
                                    [
                                        'name' =>'goods_sn',
                                        'title'=>$modelG->getAttributeLabel('goods_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' =>'goods_num',
                                        'title'=>$modelG->getAttributeLabel('goods_num'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:60px'
                                        ]
                                    ],
                                    [
                                        'name' => "product_type_id",
                                        'title'=>$modelG->getAttributeLabel('product_type_id'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->styleService->productType->getDropDown()
                                    ],
                                    [
                                        'name' => "style_cate_id",
                                        'title'=>$modelG->getAttributeLabel('style_cate_id'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->styleService->styleCate->getDropDown()
                                    ],
                                    [
                                        'name' => "style_sex",
                                        'title'=>$modelG->getAttributeLabel('style_sex'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \addons\Style\common\enums\StyleSexEnum::getMap()
                                    ],
                                    [
                                        'name' => "style_channel_id",
                                        'title'=>$modelG->getAttributeLabel('style_channel_id'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->styleService->styleChannel->getDropDown()
                                    ],
                                    [
                                        'name' =>'style_sn',
                                        'title'=>$modelG->getAttributeLabel('style_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px'
                                        ]
                                    ],
                                    [
                                        'name' =>'qiban_sn',
                                        'title'=>$modelG->getAttributeLabel('qiban_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px'
                                        ]
                                    ],
                                    [
                                        'name' => "qiban_type",
                                        'title'=>$modelG->getAttributeLabel('qiban_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \addons\Style\common\enums\QibanTypeEnum::getMap()
                                    ],
                                    [
                                        'name' => "factory_mo",
                                        'title'=>$modelG->getAttributeLabel('factory_mo'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "finger_hk",
                                        'title'=>$modelG->getAttributeLabel('finger_hk'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'defaultValue' => 0,
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO)
                                    ],
                                    [
                                        'name' => "finger",
                                        'title'=>$modelG->getAttributeLabel('finger'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'defaultValue' => 0,
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::FINGER)
                                    ],
                                    [
                                        'name' => "xiangkou",
                                        'title'=>$modelG->getAttributeLabel('xiangkou'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'defaultValue' => 0,
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU)
                                    ],
                                    [
                                        'name' => "material",
                                        'title'=>$modelG->getAttributeLabel('material'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'disabled'=>'disabled',
                                            'prompt'=>'请选择',
                                        ],
                                        'defaultValue' => 0,
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL)
                                    ],
                                    [
                                        'name' => "material_type",
                                        'title'=>$modelG->getAttributeLabel('material_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'style'=>'width:100px',
                                            'readonly' =>'true',
                                            'prompt'=>'请选择',
                                        ],
                                        'defaultValue' => 0,
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE)
                                    ],
                                    [
                                        'name' => "material_color",
                                        'title'=>$modelG->getAttributeLabel('material_color'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'style'=>'width:100px',
                                            'readonly' =>'true',
                                            'prompt'=>'请选择',
                                        ],
                                        'defaultValue' => 0,
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR)
                                    ],
                                    [
                                        'name' => "gold_weight",
                                        'title'=>$modelG->getAttributeLabel('gold_weight'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "gold_price",
                                        'title'=>$modelG->getAttributeLabel('gold_price'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "gold_loss",
                                        'title'=>$modelG->getAttributeLabel('gold_loss'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "gold_amount",
                                        'title'=>$modelG->getAttributeLabel('gold_amount'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "gross_weight",
                                        'title'=>$modelG->getAttributeLabel('gross_weight'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "suttle_weight",
                                        'title'=>$modelG->getAttributeLabel('suttle_weight'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "jintuo_type",
                                        'title'=>$modelG->getAttributeLabel('jintuo_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \addons\Style\common\enums\JintuoTypeEnum::getMap()
                                    ],
                                    [
                                        'name' => "is_inlay",
                                        'title'=>$modelG->getAttributeLabel('is_inlay'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \addons\Style\common\enums\InlayEnum::getMap()
                                    ],
                                    [
                                        'name' => "kezi",
                                        'title'=>$modelG->getAttributeLabel('kezi'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "goods_color",
                                        'title'=>$modelG->getAttributeLabel('goods_color'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::GOODS_COLOR)
                                    ],
                                    [
                                        'name' => "chain_long",
                                        'title'=>$modelG->getAttributeLabel('chain_long'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "chain_type",
                                        'title'=>$modelG->getAttributeLabel('chain_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::CHAIN_TYPE)
                                    ],
                                    [
                                        'name' => "cramp_ring",
                                        'title'=>$modelG->getAttributeLabel('cramp_ring'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::CHAIN_BUCKLE)
                                    ],
                                    [
                                        'name' => "talon_head_type",
                                        'title'=>$modelG->getAttributeLabel('talon_head_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'style'=>'width:80px',
                                            'readonly' =>'true',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::TALON_HEAD_TYPE)
                                    ],
                                    [
                                        'name' => "xiangqian_craft",
                                        'title'=>$modelG->getAttributeLabel('xiangqian_craft'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::XIANGQIAN_CRAFT)
                                    ],
                                    [
                                        'name' => "product_size",
                                        'title'=>$modelG->getAttributeLabel('product_size'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "cert_id",
                                        'title'=>$modelG->getAttributeLabel('cert_id'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "cert_type",
                                        'title'=>$modelG->getAttributeLabel('cert_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CERT_TYPE)
                                    ],
                                    [
                                        'name' => "cost_price",
                                        'title'=>$modelG->getAttributeLabel('cost_price'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "market_price",
                                        'title'=>$modelG->getAttributeLabel('market_price'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "sale_price",
                                        'title'=>$modelG->getAttributeLabel('sale_price'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "main_stone",
                                        'title'=>$modelG->getAttributeLabel('main_stone'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE)
                                    ],
                                    [
                                        'name' => "main_stone_sn",
                                        'title'=>$modelG->getAttributeLabel('main_stone_sn'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "main_cert_id",
                                        'title'=>$modelG->getAttributeLabel('main_cert_id'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "main_cert_type",
                                        'title'=>$modelG->getAttributeLabel('main_cert_type'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE)
                                    ],
                                    [
                                        'name' => "main_stone_num",
                                        'title'=>$modelG->getAttributeLabel('main_stone_num'),
                                        'enableError'=>false,
                                        'defaultValue' => 0,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "main_stone_weight",
                                        'title'=>$modelG->getAttributeLabel('main_stone_weight'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "main_stone_shape",
                                        'title'=>$modelG->getAttributeLabel('main_stone_shape'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SHAPE)
                                    ],
                                    [
                                        'name' => "main_stone_color",
                                        'title'=>$modelG->getAttributeLabel('main_stone_color'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOR)
                                    ],
                                    [
                                        'name' => "main_stone_clarity",
                                        'title'=>$modelG->getAttributeLabel('main_stone_clarity'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CLARITY)
                                    ],
                                    [
                                        'name' => "main_stone_cut",
                                        'title'=>$modelG->getAttributeLabel('main_stone_cut'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CUT)
                                    ],
                                    [
                                        'name' => "main_stone_symmetry",
                                        'title'=>$modelG->getAttributeLabel('main_stone_symmetry'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SYMMETRY)
                                    ],
                                    [
                                        'name' => "main_stone_polish",
                                        'title'=>$modelG->getAttributeLabel('main_stone_polish'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_POLISH)
                                    ],
                                    [
                                        'name' => "main_stone_fluorescence",
                                        'title'=>$modelG->getAttributeLabel('main_stone_fluorescence'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_FLUORESCENCE)
                                    ],
                                    [
                                        'name' => "main_stone_colour",
                                        'title'=>$modelG->getAttributeLabel('main_stone_colour'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOUR)
                                    ],
                                    [
                                        'name' => "main_stone_size",
                                        'title'=>$modelG->getAttributeLabel('main_stone_size'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "main_stone_price",
                                        'title'=>$modelG->getAttributeLabel('main_stone_price'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_cert_id1",
                                        'title'=>$modelG->getAttributeLabel('second_cert_id1'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone1",
                                        'title'=>$modelG->getAttributeLabel('second_stone1'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE)
                                    ],
                                    [
                                        'name' => "second_stone_num1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_num1'),
                                        'enableError'=>false,
                                        'defaultValue' => 0,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone_weight1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_weight1'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone_shape1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_shape1'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE)
                                    ],
                                    [
                                        'name' => "second_stone_color1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_color1'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR)
                                    ],
                                    [
                                        'name' => "second_stone_clarity1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_clarity1'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY)
                                    ],
                                    [
                                        'name' => "second_stone_size1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_size1'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone_price1",
                                        'title'=>$modelG->getAttributeLabel('second_stone_price1'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone2",
                                        'title'=>$modelG->getAttributeLabel('second_stone2'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE)
                                    ],
                                    [
                                        'name' => "second_stone_num2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_num2'),
                                        'enableError'=>false,
                                        'defaultValue' => 0,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone_weight2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_weight2'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone_shape2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_shape2'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE)
                                    ],
                                    [
                                        'name' => "second_stone_color2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_color2'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR)
                                    ],
                                    [
                                        'name' => "second_stone_clarity2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_clarity2'),
                                        'enableError'=>false,
                                        'type'  => 'dropDownList',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY)
                                    ],
                                    [
                                        'name' => "second_stone_size2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_size2'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "second_stone_price2",
                                        'title'=>$modelG->getAttributeLabel('second_stone_price2'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "markup_rate",
                                        'title'=>$modelG->getAttributeLabel('markup_rate'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "gong_fee",
                                        'title'=>$modelG->getAttributeLabel('gong_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "parts_weight",
                                        'title'=>$modelG->getAttributeLabel('parts_weight'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "parts_price",
                                        'title'=>$modelG->getAttributeLabel('parts_price'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "parts_fee",
                                        'title'=>$modelG->getAttributeLabel('parts_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "xianqian_fee",
                                        'title'=>$modelG->getAttributeLabel('xianqian_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "biaomiangongyi",
                                        'title'=>$modelG->getAttributeLabel('biaomiangongyi'),
                                        'type' => 'dropDownList',
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:100px',
                                            'prompt'=>'请选择',
                                        ],
                                        'items' => \Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK)
                                    ],
                                    [
                                        'name' => "biaomiangongyi_fee",
                                        'title'=>$modelG->getAttributeLabel('biaomiangongyi_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "fense_fee",
                                        'title'=>$modelG->getAttributeLabel('fense_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:100px'
                                        ]
                                    ],
                                    [
                                        'name' => "bukou_fee",
                                        'title'=>$modelG->getAttributeLabel('bukou_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "cert_fee",
                                        'title'=>$modelG->getAttributeLabel('cert_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "extra_stone_fee",
                                        'title'=>$modelG->getAttributeLabel('extra_stone_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "tax_fee",
                                        'title'=>$modelG->getAttributeLabel('tax_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' => "other_fee",
                                        'title'=>$modelG->getAttributeLabel('other_fee'),
                                        'enableError'=>false,
                                        'defaultValue' => '0.00',
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'type' => 'number',
                                            'style'=>'width:80px'
                                        ]
                                    ],
                                    [
                                        'name' =>'barcode',
                                        'title'=>$modelG->getAttributeLabel('barcode'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:120px'
                                        ]
                                    ],
                                    [
                                        'name' => "goods_remark",
                                        'title'=>$modelG->getAttributeLabel('goods_remark'),
                                        'enableError'=>false,
                                        'options' => [
                                            'class' => 'input-priority',
                                            'readonly' =>'true',
                                            'style'=>'width:80px'
                                        ]
                                    ]
                                ]
                            ])->label(false) ?>
                        </div>
                    </div>
                </div>
                <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function searchReceiptGoods() {
        var produce_sns = $.trim($("#purchasereceiptform-produce_sns").val());
        if(!produce_sns) {
            rfMsg("请输入布产单编号");
            return false;
        }
        var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['produce_sns','search',])?>&search=1&produce_sns="+produce_sns;
        window.location.href = url;
    }
</script>
