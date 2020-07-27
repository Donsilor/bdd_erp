<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\enums\AppEnum;
use common\helpers\DebrisHelper;
use common\enums\AddonsEnum;

$this->title = '行为日志';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover'],
                    'columns' => [
                        'id',
                        [
                                'attribute' => 'module',
                                'filter' => Html::activeDropDownList($searchModel, 'module', AddonsEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control'
                                ]),
                                'value' => function ($model) {
                                    return AddonsEnum::getValue($model->module)."<br/>".$model->module;
                                },
                                'headerOptions' => ['class' => 'col-md-1'],
                                'format' => 'raw',
                        ], 
                        [
                                'label' => '对象',
                                'attribute' => 'object',
                                'value' => function ($model) {
                                    return $model->object.'<br/>'.$model->controller;
                                },
                                'filter' => Html::activeTextInput($searchModel, 'object', [
                                        'class' => 'form-control',
                                        'style'=>'width:150px'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [                                
                                'attribute' => 'behavior',
                                'value' => function ($model) {
                                    return $model->behavior."<br/>".$model->action;
                                },
                                'filter' => Html::activeTextInput($searchModel, 'behavior', [
                                        'class' => 'form-control',
                                        'style'=>'width:150px'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        /* 'url',
                        [
                            'label' => '位置信息',
                            'value' => function ($model) {
                                $str = [];
                                $str[] = DebrisHelper::analysisIp($model->ip);
                                $str[] = DebrisHelper::long2ip($model->ip);
                                return implode('</br>', $str);
                            },
                            'format' => 'raw',
                        ], */
                        'remark',
                        [
                                'label' => '用户',
                                'value' => function ($model) {
                                    return Yii::$app->services->backend->getUserName($model);
                                },
                                'filter' => false, //不显示搜索框
                                'format' => 'raw',
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => false, //不显示搜索框
                            'format' => ['date', 'php:Y-m-d H:i:s'],
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::linkButton(['view', 'id' => $model->id], '查看详情', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
