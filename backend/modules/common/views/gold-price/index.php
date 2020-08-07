<?php
use common\helpers\Html;
use yii\grid\GridView;

$this->title = '部门管理';
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
                        'data-target' => '#ajaxModalLg',
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

                        ],

                        [
                            'attribute' => 'code',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index){
                                return $model->code;
                            },

                        ],
                        [
                            'attribute' => 'price',
                            'value' => function ($model, $key, $index){
                                return $model->price;
                            },
                            'filter' => false

                        ],
                        [
                            'attribute' => 'usd_price',
                            'value' => function ($model, $key, $index){
                                return $model->usd_price;
                            },
                            'filter' => false

                        ],
                        [
                            'attribute' => 'rmb_rate',
                            'value' => function ($model, $key, $index){
                                return $model->rmb_rate;
                            },
                            'filter' => false

                        ],


                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template'=> '{edit} {status}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-edit','id' => $model->id], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
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
