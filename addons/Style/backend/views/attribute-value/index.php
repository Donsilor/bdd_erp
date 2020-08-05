<?php

use common\helpers\Url;
use common\helpers\Html;
use yii\grid\GridView;

$this->title = '属性值管理';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">                
                <li><a href="<?= Url::to(['attribute/index']) ?>"> 属性管理</a></li>
                <li class="active"><a href="<?= Url::to(['attribute-value/index']) ?>"> 属性值管理</a></li>
                <li class="pull-right">
                    <?= Html::create(['ajax-edit', 'cate_id' => $cate_id], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                </li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane">
                    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'tableOptions' => ['class' => 'table table-hover'],
                            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => false,
                                ],
                                'id',
                                [
                                        'attribute'=>'code',
                                ],
                                [
                                    'attribute'=>'lang.attr_value_name',
                                ], 
                                [
                                    'attribute' => 'sort',
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                                    'value' => function ($model, $key, $index, $column){
                                        return  Html::sort($model->sort,['data-url'=>Url::to(['attribute-value/ajax-update'])]);
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                                    'value' => function ($model){
                                        return \common\enums\StatusEnum::getValue($model->status);
                                    }
                                ],            
                                [
                                    'attribute'=>'updated_at',
                                    'value' => function ($model) {
                                        return Yii::$app->formatter->asDatetime($model->updated_at);
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => '操作',
                                    'template' => '{edit} {status} {delete}',
                                    'buttons' => [
                                    'edit' => function($url, $model, $key){                
                                        return Html::edit(['attribute-value/ajax-edit-lang','id' => $model->id,'returnUrl'=>Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
                                        ]);
                                    },
                                   'status' => function($url, $model, $key){
                                            return Html::status($model->status,['data-url'=>Url::to(['attribute-value/ajax-update'])]);
                                    },
                                    'delete' => function($url, $model, $key){
                                            return Html::delete(['attribute-value/delete', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()]);
                                    },
                                    ]
                                ]
                        ]
                  ]); ?>		
                </div>
            </div>
        </div>
    </div>
</div>