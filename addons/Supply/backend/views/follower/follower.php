<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('style_channel', '跟单人');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">供应商详情 - <?php echo $supplier->supplier_name?></h2>
    <ul class="nav nav-tabs">
        <li class=""><a href="<?= \common\helpers\Url::to(['supplier/view','id'=>$supplier->id]) ?>" >供应商</a></li>
        <li class="active"><a href="<?= \common\helpers\Url::to(['follower/index','supplier_id'=>$supplier->id]) ?>" >跟单人</a></li>
        <div style="float: right"><?= Html::create(['ajax-edit','supplier_id'=>$supplier->id], '创建', [
                'data-toggle' => 'modal',
                'data-target' => '#ajaxModal',
                'class'=>'btn btn-primary btn-xs',
            ]); ?></div>
    </ul>


    <div class="row">
        <div class="col-xs-12">
            <div class="box" style="margin-top: 0">

                <div class="box-body table-responsive">
                    <?php echo Html::batchButtons(false)?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],

                            [
                                'attribute' => 'id',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],

                            [
                                'label' => '跟单人',
                                'attribute'=>'member.username',

                                'headerOptions' => [],
                            ],

                            [
                                'attribute'=>'updated_at',
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->updated_at);
                                }

                            ],


                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \common\enums\StatusEnum::getValue($model->status);
                                },
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{status} {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key){
                                        return Html::edit(['ajax-edit','id' => $model->id,'returnUrl' => Url::getReturnUrl()], '编辑', [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModal',
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
</div>