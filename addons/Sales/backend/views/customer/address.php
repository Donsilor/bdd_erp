<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '客户信息';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?= $this->title; ?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?= Html::create(['ajax-edit-address','customer_id'=>Yii::$app->request->get('customer_id')], '添加地址', [
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModalLg',
        ]); ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
            <div class="box-body table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    //'options' => ['style'=>'white-space:nowrap;'],
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible' => false,
                        ],
                        [
                            'attribute' => 'id',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'realname',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'mobile',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'email',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'country_name',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'province_name',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'city_name',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ], 
                        [
                                'attribute' => 'address_details',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                                'attribute' => 'zip_code',
                                'filter' => false,
                                'format' => 'raw',
                                //'headerOptions' => ['class' => 'col-md-1'],
                        ],                        
                        [
                            'attribute'=>'created_at',
                            'value'=>function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'filter' => false,
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ], 
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {delete}',
                            'buttons' => [
                               'edit' => function($url, $model, $key){
                                    return Html::edit(['ajax-edit-address','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                    ]);
                                },                                        
                                'delete' => function($url, $model, $key){
                                    return Html::delete(['delete-address', 'id' => $model->id]);
                                },
                              ],
                        ],
                    ]
                  ]);
                ?>
            </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>
