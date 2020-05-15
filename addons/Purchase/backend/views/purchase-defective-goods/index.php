<?php

use common\enums\AreaEnum;
use common\enums\StatusEnum;
use common\helpers\AmountHelper;
use common\helpers\Html;
use common\helpers\Url;
use unclead\multipleinput\MultipleInput;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = '不良返厂单详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['action' => Url::to(['ajax-edit'])]); ?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $defectiveInfo->receipt_no ?></h2>
    <?php echo Html::menuTab($tabList, $tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                    <?php //echo Html::checkboxList('colmun','',\Yii::$app->purchaseService->purchaseGoods->listColmuns(1))?>
                    </h3>
                    <div class="box-tools">
                        <?= Html::create(['edit', 'receipt_id' => $defectiveInfo->id], '新增货品', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width'=>'90%',
                            'data-height'=>'90%',                            
                            'data-offset'=>'20px',
                        ]); ?>
                    </div>
               </div>
            <div class="box-body table-responsive">
                <div class="tab-content">
                    <?php
                    $defectiveColomns = [
                        [
                            'name' => 'id',
                            'title'=>"ID",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:60px'
                            ]
                        ],
                        [
                            'name' =>'defective_id',
                            'title'=>"返厂单ID",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:60px'
                            ]
                        ],
                        [
                            'name' =>'receipt_goods_id',
                            'title'=>"收货单货品序号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:100px'
                            ]
                        ],
                        [
                            'name' =>'produce_sn',
                            'title'=>"布产单编号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:100px'
                            ]
                        ],
                        [
                            'name' =>'style_sn',
                            'title'=>"款号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:100px'
                            ]
                        ],
                        [
                            'name' => "factory_mo",
                            'title'=>"工厂模号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:100px'
                            ]
                        ],
                        [
                            'name' => "style_cate_id",
                            'title'=>"款式分类",
                            'enableError'=>false,
                            'type'  => 'dropDownList',
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:100px'
                            ],
                            'items' => Yii::$app->styleService->styleCate->getDropDown()
                        ],
                        [
                            'name' => "product_type_id",
                            'title'=>"产品线",
                            'enableError'=>false,
                            'type'  => 'dropDownList',
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:100px'
                            ],
                            'items' => Yii::$app->styleService->productType->getDropDown()
                        ],
                        [
                            'name' => "cost_price",
                            'title'=>"金额",
                            'enableError'=>false,
                            'defaultValue' => '0.00',
                            'options' => [
                                'class' => 'input-priority',
                                'type' => 'number',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "oqc_reason",
                            'title'=>"质检未过原因",
                            'enableError'=>false,
                            'type'  => 'dropDownList',
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:80px'
                            ],
                            'items' => Yii::$app->purchaseService->purchaseFqcConfig->getDropDown()
                        ],
                        [
                            'name' => "goods_remark",
                            'title'=>"商品备注",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:80px'
                            ]
                        ]
                    ];
                    ?>
                    <?= unclead\multipleinput\MultipleInput::widget([
                        'name' => "receipt_goods_list",
                        'value' => $defectiveGoods,
                        'columns' => $defectiveColomns,
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 text-center">
                    <?= $form->field($defectiveInfo, 'id')->hiddenInput()->label(false) ?>
                    <button class="btn btn-primary" type="submit">保存</button>
                    <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>