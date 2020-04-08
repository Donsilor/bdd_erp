<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\ImageHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '款式管理';
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
                        'data-target' => '#ajaxModalLg',
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
                    'attribute' => 'style_image',
                    'value' => function ($model) {
                        return ImageHelper::fancyBox($model->style_image);
                    },
                    'filter' => false,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],  
            ],  
            [
                    'attribute' => 'style_sn',
                    'value'=>function($model) {
                         return Html::a($model->style_sn, ['view', 'id' => $model->id], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'130'],
            ],
            [
                    'headerOptions' => ['width'=>'400'],
                    'attribute' => 'style_name',
                    'value' => 'style_name',
                    'filter' => Html::activeTextInput($searchModel, 'style_name', [
                          'class' => 'form-control',
                    ]),
                    'format' => 'raw',   
            ],            
            [
                    'label' => '款式分类',
                    'attribute' => 'cate.name',
                    'value' => "cate.name",
                    'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getGrpDropDown(), [
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
                    'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getGrpDropDown(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
            ],  
            [
                    'attribute' => 'sale_price',
                    'value' => "sale_price",
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'100'],
            ],
            [
                    'attribute' => 'goods_num',
                    'value' => "goods_num",
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
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
                'template' => '{edit} {audit} {status} {delete}',
                'buttons' => [
                    'edit' => function($url, $model, $key){
                        return Html::edit(['edit-info','id' => $model->id,'returnUrl' => Url::getReturnUrl()]);
                    },
                    'audit' => function($url, $model, $key){
                        if($model->audit_status != 1){
                            return Html::batchAudit(['ajax-batch-audit'],'审核',['class'=>'btn btn-success btn-sm']);
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
