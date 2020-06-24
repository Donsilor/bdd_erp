<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use addons\Warehouse\common\enums\StoneStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone', '石料列表');
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
                    'options' => ['style'=>'width:120%;'],
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
                            'attribute'=>'stone_sn',
                            'filter' => Html::activeTextInput($searchModel, 'stone_sn', [
                                'class' => 'form-control',
                                'style' => 'width:120px',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'stone_name',
                            'filter' => Html::activeTextInput($searchModel, 'stone_name', [
                                'class' => 'form-control',
                                'style' => 'width:150px',
                            ]),
                            'headerOptions' => [],
                        ],                        
                        [
                            'attribute'=>'style_sn',
                            'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style' => 'width:100px',
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute' => 'stone_type',
                            'value' => function ($model){
                                return Yii::$app->attr->valueName($model->stone_type);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_type',Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE), [
                                'prompt' => '全部',
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => ['width'=>'100'],
                        ],
                        [
                            'attribute' => 'stone_status',
                            'value' => function ($model){
                                return StoneStatusEnum::getValue($model->stone_status);
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'stone_status',StoneStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'stock_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'stock_cnt', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        [
                            'attribute'=>'stock_weight',
                            'filter' => Html::activeTextInput($searchModel, 'stock_weight', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],
                        /*[
                            'attribute'=>'stone_price',
                            'filter' => Html::activeTextInput($searchModel, 'stone_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],*/
                        [
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                                'style'=> 'width:100px;'
                            ]),
                            'headerOptions' => [],
                        ],                        
                        [
                            'attribute' => 'stone_color',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_color);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'stone_clarity',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_clarity);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'stone_symmetry',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_symmetry);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'stone_polish',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_polish);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute' => 'stone_fluorescence',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_fluorescence);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'attribute'=>'created_at',
                            'filter' => DateRangePicker::widget([    // 日期组件
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'value' => $searchModel->created_at,
                                'options' => ['readonly' => false,'class'=>'form-control','style'=>'width:100px;'],
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
                        /*[
                            'label' => '买入',
                            'attribute'=>'ms_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ms_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '分包转入',
                            'attribute'=>'fenbaoru_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'fenbaoru_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '送出',
                            'attribute'=>'ss_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ss_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '分包转出',
                            'attribute'=>'fenbaochu_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'fenbaochu_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '还回',
                            'attribute'=>'hs_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'hs_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '退石',
                            'attribute'=>'ts_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ts_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '退货',
                            'attribute'=>'th_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'th_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '遗失',
                            'attribute'=>'ys_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ys_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '损坏',
                            'attribute'=>'sy_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'sy_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '其他入库',
                            'attribute'=>'rk_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'rk_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '其他出库',
                            'attribute'=>'ck_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'ck_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],*/
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