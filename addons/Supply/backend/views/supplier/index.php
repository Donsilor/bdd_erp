<?php

use common\helpers\Html;
use common\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use common\helpers\ImageHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$id = $searchModel->id;

$this->title = Yii::t('supply_supplier', '供应商管理');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools" style="right: 100px;">
                    <?= Html::create(['edit-lang']) ?>
                    <?= Html::a('导出Excel','export?goods_name=') ?>
                </div>

            </div>
            <div class="box-body table-responsive">
    <?php echo Html::batchButtons(false)?>         
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
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
                'value' => 'id',
                'filter' => Html::activeTextInput($searchModel, 'id', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'supplier_name',
                'value' => 'supplier_name',
                'filter' => Html::activeTextInput($searchModel, 'supplier_name', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'200'],
            ],
            [
                'attribute' => 'business_scope',
                'value' => 'business_scope',
                'filter' => Html::activeTextInput($searchModel, 'business_scope', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'contactor',
                'value' => 'contactor',
                'filter' => Html::activeTextInput($searchModel, 'contactor', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'telephone',
                'value' => 'telephone',
                'filter' => Html::activeTextInput($searchModel, 'telephone', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'mobile',
                'value' => 'mobile',
                'filter' => Html::activeTextInput($searchModel, 'mobile', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'address',
                'value' => 'address',
                'filter' => Html::activeTextInput($searchModel, 'address', [
                    'class' => 'form-control',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'120'],
            ],
            [
                'attribute' => 'audit_time',
                'filter' => DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'audit_time',
                    'value' => '',
                    'options' => ['readonly' => true, 'class' => 'form-control',],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'locale' => [
                            'separator' => '/',
                        ],
                        'endDate' => date('Y-m-d', time()),
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'todayBtn' => 'linked',
                        'clearBtn' => true,
                    ],
                ]),
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->audit_time);
                },
                'format' => 'raw',
                'headerOptions' => ['width'=>'200'],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
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
                        return Html::edit(['edit-lang', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()]);
                },
                'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                  },
                    /*'delete' => function($url, $model, $key){
                            return Html::delete(['delete', 'id' => $model->id]);
                    },
                    'view'=> function($url, $model, $key){
                        return Html::a('预览', \Yii::$app->params['frontBaseUrl'].'/diamond-details/'.$model->id.'?goodId='.$model->id.'&backend=1',['class'=>'btn btn-info btn-sm','target'=>'_blank']);
                    },
                    'show_log' => function($url, $model, $key){
                        return Html::linkButton(['goods-log/index','id' => $model->id, 'type_id' => $model->type_id, 'returnUrl' => Url::getReturnUrl()], '日志');
                    },*/
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
