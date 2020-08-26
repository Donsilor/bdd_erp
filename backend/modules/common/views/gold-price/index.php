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
                            'attribute' => 'refer_price',
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
                            'attribute' => 'notice_status',
                            'value' => function ($model, $key, $index){
                                return common\enums\ConfirmEnum::getValue($model->notice_status);
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'notice_users',
                            'value' => function ($model, $key, $index){
                                if($model->notice_users) {
                                    $model->notice_users = explode(',',$model->notice_users);
                                    $users = Yii::$app->services->backendMember->findAllByIds($model->notice_users);
                                    $str = '';
                                    foreach ($users as $user) {
                                        $str .= $user->username.'('.$user->mobile.')<br/>';
                                    }
                                    return $str;
                                }
                            },
                            'filter' => false,
                            'headerOptions' => ['class' => 'col-md-2'],
                            'format' => 'raw',
                       ],
                        [
                            'attribute'=>'sync_time',
                            'value'=>function($model){
                                $sync_type = common\enums\OperateTypeEnum::getValue($model->sync_type);
                                $sync_time =  Yii::$app->formatter->asDatetime($model->sync_time);
                                return $sync_type.'('.$model->sync_user.')<br/>'.$sync_time;
                            },
                            'filter' => false,   
                            'format' => 'raw',
                        ],

                        [
                            'header' => "操作",
                            'class' => 'yii\grid\ActionColumn',
                            'template'=> '{edit} {price} {status}',
                            'buttons' => [
                                'edit' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-edit','id' => $model->id], '编辑', [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModal',
                                    ]);
                                },
                                'price' => function ($url, $model, $key) {
                                    return Html::edit(['ajax-price','id' => $model->id], '更新金价', [
                                            'class' => 'btn btn-success btn-sm',
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
