<?php

use yii\grid\GridView;
use common\helpers\Html;
use common\helpers\ImageHelper;

$this->title = '客户列表';
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    //重新定义分页样式
                    'tableOptions' => ['class' => 'table table-hover rf-table'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false, // 不显示#
                        ],
                        [
                            'attribute' => 'id',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'head_portrait',
                            'value' => function ($model) {
                                return Html::img(ImageHelper::defaultHeaderPortrait(Html::encode($model->head_portrait)),
                                    [
                                        'class' => 'img-circle rf-img-md img-bordered-sm',
                                    ]);
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'lastname',
                            'filter' => true,
                        ],
                        [
                            'attribute' => 'firstname',
                            'filter' => true,
                        ],
                        [
                            'attribute' => 'realname',
                            'filter' => true,
                        ],
                        [
                            'attribute' => 'gender',
                            'format' => 'raw',
                            'value' => function ($model){
                                return \common\enums\GenderEnum::getValue($model->gender);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'gender',\common\enums\GenderEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'marriage',
                            'format' => 'raw',
                            'value' => function ($model){
                                return \addons\Sales\common\enums\MarriageEnum::getValue($model->marriage);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'marriage',\addons\Sales\common\enums\MarriageEnum::getMap(), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'mobile',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'home_phone',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'qq',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{ajax-edit} {address} {recharge} {edit} {status} {destroy}',
                            'buttons' => [
                                'ajax-edit' => function ($url, $model, $key) {
                                    return Html::linkButton(['ajax-edit', 'id' => $model->id], '账号密码', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);
                                },
                                'address' => function ($url, $model, $key) {
                                    return Html::linkButton(['address/index', 'member_id' => $model->id], '收货地址');
                                },
                                'recharge' => function ($url, $model, $key) {
                                    return Html::linkButton(['recharge', 'id' => $model->id], '充值', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);
                                },
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['edit', 'id' => $model->id]);
                                },
                                'status' => function ($url, $model, $key) {
                                    return Html::status($model->status);
                                },
                                'destroy' => function ($url, $model, $key) {
                                    return Html::delete(['destroy', 'id' => $model->id]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>