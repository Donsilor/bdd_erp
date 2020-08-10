<?php
use common\helpers\Html;
use yii\grid\GridView;
use common\helpers\AmountHelper;

$this->title = '金价管理';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title; ?></h3>
                <div class="box-tools">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ])?>
                </div>
            </div>

            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    //'options' => ['style'=>'white-space:nowrap;'],
                    'showFooter' => false,//显示footer行
                    'id'=>'grid',
                    'columns' => [


                        [
                            'attribute'=>'id',
                            'value'=> 'id',
                            'headerOptions'=>['style'=>'width:50px;'],

                        ],
                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index){
                                return $model->name;
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],

                        [
                            'attribute' => 'code',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index){
                                return $model->code;
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'price',
                            'value' => function ($model, $key, $index){
                                return $model->price;
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],

                        ],
                        [
                            'label' => '参考金价(元/克)',
                            'value' => function ($model, $key, $index){
                                return \Yii::$app->goldTool->getGoldRmbPrice($model->code);
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                       ],
                       [
                            'attribute' => 'notice_range',
                            'value' => function ($model, $key, $index){
                                return $model->notice_range;
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'notice_users',
                            'value' => function ($model, $key, $index){
                                return $model->notice_users;
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-2'],
                       ],
                        [
                            'attribute'=>'api_time',
                            'value'=>function($model){
                                return Yii::$app->formatter->asDatetime($model->api_time);
                            },
                            'filter' => false,                       
                        ],

                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template'=> '{edit} {status}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-edit','id' => $model->id], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);
                                },
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status);
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::delete(['delete','id' => $model->id]);
                                },
                            ],
                        ],
                    ]
                ]); ?>

            </div>

        </div>
    </div>
</div>
