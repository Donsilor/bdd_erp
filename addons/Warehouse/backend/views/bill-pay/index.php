<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('warehouse_bill_pay', '结算商信息');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?> - <?= $bill->bill_no?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?php if($bill->bill_status == BillStatusEnum::SAVE){ ?>
                    <?= Html::create(['ajax-edit', 'bill_id' => $bill->id,'returnUrl' => Url::getReturnUrl()], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
                    <?php }?>
                </div>
            </div>
            <div class="box">
                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'attribute' => 'supplier_id',
                                'value' =>"supplier.supplier_name",
                                'filter'=>Select2::widget([
                                    'name'=>'SearchModel[supplier_id]',
                                    'value'=>$searchModel->supplier_id,
                                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-4', 'style'=> 'width:120px;'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2', 'style'=> 'width:120px;'],
                            ],
                            [
                                'attribute' => 'pay_content',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\PayContentEnum::getValue($model->pay_content);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pay_content',\addons\Warehouse\common\enums\PayContentEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'

                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2','style'=>'width:120px;'],
                            ],
                            [
                                'attribute' => 'pay_method',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\PayMethodEnum::getValue($model->pay_method);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pay_method',\addons\Warehouse\common\enums\PayMethodEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2','style'=>'width:120px;'],
                            ],
                            [
                                'attribute' => 'pay_tax',
                                'value' => function ($model){
                                    return \addons\Warehouse\common\enums\PayTaxEnum::getValue($model->pay_tax);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'pay_tax',\addons\Warehouse\common\enums\PayTaxEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:120px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2','style'=>'width:120px;'],
                            ],
                            [
                                'attribute'=>'pay_amount',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2','style'=>'width:100px;'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => ' {edit} {delete} ',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'bill_id' => $model->bill_id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModal',
                                            ]);
                                        }
                                    },
                                    'status' => function($url, $model, $key){
                                        return Html::status($model->status);
                                    },
                                    'delete' => function($url, $model, $key) use($bill) {
                                        if($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                                        }
                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-2','style'=>'width:100px;'],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
            <!-- box end -->
        </div>
    </div>
</div>