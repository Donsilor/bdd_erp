<?php

use common\helpers\Html;
use common\helpers\ImageHelper;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '销售平台列表';
$this->params['breadcrumbs'][] = $this->title;

$params = Yii::$app->request->queryParams;
$params = $params ? "&".http_build_query($params) : '';

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools" style="right: 100px;">
                    <?= Html::create(['ajax-edit'], '创建', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
                    ]); ?>
                </div>                
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'options' => ['style'=>'white-space:nowrap;'],
                    'showFooter' => false,//显示footer行
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
                        /* [
                            'attribute'=>'code',
                            'format' =>'raw',
                            'value'=>'code',
                            'filter' => Html::activeTextInput($searchModel, 'code', [
                                 'class' => 'form-control',
                            ]),
                             'headerOptions' => ['width'=>'80'],
                        ], */
                        [
                            'attribute'=>'name',
                            'format' => 'raw',
                            'value'=>'name',
                            'filter' => Html::activeTextInput($searchModel, 'name', [
                                'class' => 'form-control',
                            ]),
                                'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                            'attribute'=>'channel_id',
                             'value' =>function($model) {
                                 return $model->channel->name??'';
                             },
                            'filter' => Html::activeDropDownList($searchModel, 'channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    
                            ]),
                           'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute'=>'realname',
                            'filter' => Html::activeTextInput($searchModel, 'realname', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'mobile',
                            'value' => 'mobile',
                            'format' => 'raw',
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'address_details',
                            'value' => function($model){
                                return $model->address_details;
                            },
                            'filter' => false,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],                        
                        [
                            'label' => '添加人',
                            'attribute' => 'creator.username',
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => false,
                        ], 
                        [
                            'attribute' => 'updated_at',
                            'value' =>function($model){
                                 return Yii::$app->formatter->asDatetime($model->updated_at);
                            },
                            'headerOptions' => ['class' => 'col-md-1'],
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'sort',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $column){
                                return  Html::sort($model->sort,['data-url'=>Url::to(['ajax-update'])]);
                            },
                            'headerOptions' => ['width' => '80'],
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                            'value' => function ($model){
                                return \common\enums\StatusEnum::getValue($model->status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                            ]),
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {status}',
                            'buttons' => [
                                'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);
                                },                                
                                'status' => function($url, $model, $key){
                                     return Html::status($model->status);
                                },
                                'delete' => function($url, $model, $key){
                                    return Html::delete(['delete', 'id' => $model->id]);
                                },
                            ],

                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>