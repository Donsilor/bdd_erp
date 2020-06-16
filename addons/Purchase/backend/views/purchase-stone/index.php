<?php

use addons\Purchase\common\enums\PurchaseStatusEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\select2\Select2;
use addons\Warehouse\common\enums\BillStatusEnum;

use common\enums\AuditStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '石料采购单';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">  
    <?php //echo Html::batchButtons()?>                  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'options' => ['style'=>'white-space:nowrap;'],
        'showFooter' => true,//显示footer行
        'id'=>'grid',            
        'columns' => [
            [
                    'class' => 'yii\grid\SerialColumn',
                    'visible' => false,
            ],
            [
                    'class'=>'yii\grid\CheckboxColumn',
                    'name'=>'id',  //设置每行数据的复选框属性
                    'headerOptions' => ['width'=>'30'],
            ],
            [
                    'attribute' => 'id',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],  
            [
                    'attribute' => 'purchase_sn',
                    'value'=>function($model) {
                        return Html::a($model->purchase_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'130'],
            ],
            [
                    'attribute' => 'supplier_id',
                    'value' =>function($model){
                           return $model->supplier->supplier_name ?? '';
                    },                    
                    'filter'=>Select2::widget([
                            'name'=>'SearchModel[supplier_id]',
                            'value'=>$searchModel->supplier_id,
                            'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                            'options' => ['placeholder' =>"请选择"],
                            'pluginOptions' => [
                                    'allowClear' => true,                                          
                            ],
                     ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-2'],
            ],           
            [
                    'attribute' => 'total_num',
                    'value' => "total_num",
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute' => 'total_cost',
                    'value' => function ($model){
                        return $model->total_cost;
                    },
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],            
            [
                    'attribute' => 'remark',
                    'value' => "remark",
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'200'],
            ],              
            [
                    'attribute' => 'delivery_time',
                    'value' => function($model){
                        return $model->delivery_time ? Yii::$app->formatter->asDate($model->delivery_time):'';
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ], 
            [
                    'attribute' => 'created_at',
                    'value' => function($model){
                        return $model->created_at ? Yii::$app->formatter->asDate($model->created_at):'';
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
                    ], 
            [
                    'attribute' => 'creator_id',
                    'value' => "creator.username",
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'audit_time',
                'value' => function($model){
                    return $model->audit_time ? Yii::$app->formatter->asDate($model->audit_time):'';
                },
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'audit_status',
                    'value' => function ($model){
                        return \common\enums\AuditStatusEnum::getValue($model->audit_status);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'audit_status',\common\enums\AuditStatusEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'auditor_id',
                'value' => "auditor.username",
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'purchase_status',                    
                'value' => function ($model){
                    return BillStatusEnum::getValue($model->purchase_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'purchase_status',BillStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',                        
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {audit} {goods} {apply} {follower} {delete}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        if($model->purchase_status == BillStatusEnum::SAVE){
                            return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                    'class'=>'btn btn-primary btn-sm',
                            ]);
                        }
                    },                    
                    'audit' => function($url, $model, $key){
                        if($model->purchase_status == BillStatusEnum::PENDING){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                             ]); 
                        }
                    },
                    'goods' => function($url, $model, $key){
                        return Html::a('商品列表', ['purchase-stone-goods/index', 'purchase_id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['class' => 'btn btn-warning btn-sm']);
                    },
                    'apply' => function($url, $model, $key){
                        if($model->purchase_status == BillStatusEnum::SAVE){
                            return Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'receipt' => function($url, $model, $key){
                        if($model->purchase_status == PurchaseStatusEnum::CONFIRM){
                            return Html::edit(['ajax-receipt','id'=>$model->id], '申请收货', [
                                'class'=>'btn btn-success btn-sm',
                                'onclick' => 'rfTwiceAffirm(this,"申请收货", "确定提交吗？");return false;',
                            ]);
                        }
                    },
                    'delete' => function($url, $model, $key){
                        if($model->purchase_status != BillStatusEnum::CONFIRM){
                            return Html::delete(['delete', 'id' => $model->id]);
                        }
                    },                    
                ]
            ]
        ]
      ]);
    ?>
            </div>
        </div>
    </div>
</div>
