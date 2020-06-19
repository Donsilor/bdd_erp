<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone', '金料列表');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="box-body table-responsive">
                <?php echo Html::batchButtons(false)?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
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
                        /*[
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'100'],
                        ],*/
                        [
                            'attribute'=>'gold_sn',
                            'filter' => Html::activeTextInput($searchModel, 'gold_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'supplier_id',
                            'value' =>"supplier.supplier_name",
                            'filter'=>Select2::widget([
                                'name'=>'SearchModel[supplier_id]',
                                'value'=>$searchModel->supplier_id,
                                'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                'options' => ['placeholder' =>"请选择"],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'width' => '200',
                                ],
                            ]),
                            'format' => 'raw',
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'gold_name',
                            'filter' => Html::activeTextInput($searchModel, 'gold_name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'200'],
                        ],
                        [
                            'attribute' => 'gold_type',
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->gold_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'gold_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        /*[
                            'attribute'=>'gold_num',
                            'filter' => Html::activeTextInput($searchModel, 'gold_num', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],*/
                        [
                            'attribute'=>'gold_weight',
                            'filter' => Html::activeTextInput($searchModel, 'gold_weight', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute'=>'gold_price',
                            'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
                        ],
                        [
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
                        ],
                        [
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'locale' => [
                                        'separator' => '/',
                                    ],
                                    'endDate' => date('Y-m-d',time()),
                                    'todayHighlight' => true,
                                    'autoclose' => true,
                                    'todayBtn' => 'linked',
                                    'clearBtn' => true,
                                ],
                            ]),
                            'value'=>function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'contentOptions' => ['style' => ['white-space' => 'nowrap']],
                            'template' => '',
                            'buttons' => [
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