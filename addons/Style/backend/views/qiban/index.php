<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '起版列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit','1'=>1], '有款起版', [
                        'class' => 'btn btn-primary btn-xs openIframe',
                        'data-width'=>'90%',
                        'data-height'=>'90%',
                        'data-offset'=>'20px',
                    ]); ?>
                    <?= Html::create(['edit-no-style'], '无款起版', [
                        'class' => 'btn btn-primary btn-xs openIframe',
                        'data-width'=>'90%',
                        'data-height'=>'90%',
                        'data-offset'=>'20px',
                    ]); ?>
                </div>
            </div>
            <div class="box-body table-responsive">  
    <?php //echo Html::batchButtons()?>                  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
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
                    'attribute' => 'qiban_sn',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'150'],
            ],
            [
                'attribute' => 'jintuo_type',
                'value' => function($model){
                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'150'],
            ],
            [
                    'headerOptions' => ['width'=>'300'],
                    'attribute' => 'qiban_name',
                    'value' => 'qiban_name',
                    'filter' => Html::activeTextInput($searchModel, 'qiban_name', [
                          'class' => 'form-control',
                    ]),
                    'format' => 'raw',   
            ], 
            [
                    'attribute' => 'qiban_type',
                    'value' => function($model){
                          return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                     },
                    'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                    'attribute' => 'style_sn',
                    'value' => "style_sn",
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ], 
            [
                    'label' => '款式分类',
                    'attribute' => 'cate.name',
                    'value' => "cate.name",
                    'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ], 
            [
                    'label' => '产品线',
                    'attribute' => 'type.name',
                    'value' => "type.name",
                    'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],  
            [
                    'label' => '成本价',
                    'attribute' => 'cost_price',
                    'value' => function ($model){
                        return $model->cost_price;
                    },
                    'filter' => true,
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
                    'attribute' => 'status',                    
                    'value' => function ($model){
                        return \common\enums\StatusEnum::getValue($model->status);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control',                        
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {edit} {audit} {status}',
                'buttons' => [
                    'view'=> function($url, $model, $key){
                        return Html::edit(['view','id' => $model->id,'search'=>1,'returnUrl' => Url::getReturnUrl()],'详情',[
                                'class' => 'btn btn-info btn-sm',
                        ]);                        
                    },
                    'edit' => function($url, $model, $key){
                        //审核后不能编辑
                        if($model->audit_status == 0 ){
                            if($model->qiban_type == 1){
                                return Html::edit(['edit','id' => $model->id,'search'=>1,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'class' => 'btn btn-primary btn-sm openIframe',
                                    'data-width'=>'90%',
                                    'data-height'=>'90%',
                                    'data-offset'=>'20px',
                                ]);
                            }else{
                                return Html::edit(['edit-no-style','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑',[
                                    'class' => 'btn btn-primary btn-sm openIframe',
                                    'data-width'=>'90%',
                                    'data-height'=>'90%',
                                    'data-offset'=>'20px',
                                ]);
                            }
                        }

                    },
                    'audit' => function($url, $model, $key){
                        if($model->audit_status != 1){
                            return Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                             ]); 
                        }
                    },
                    'status' => function($url, $model, $key){
                        if($model->audit_status == 1){
                            return Html::status($model->status);
                        }                        
                    },
                    'delete' => function($url, $model, $key){
                        if($model->audit_status == 0){
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
