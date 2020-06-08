<?php

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('stone', '石包列表');
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
                            'label' => '序号',
                            'attribute' => 'id',
                            'filter' => true,
                            'format' => 'raw',
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '名称',
                            'attribute'=>'stone_name',
                            'filter' => Html::activeTextInput($searchModel, 'stone_name', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'160'],
                        ],
                        [
                            'label' => '石包类型',
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
                            'label' => '库存数量',
                            'attribute'=>'stock_cnt',
                            'filter' => Html::activeTextInput($searchModel, 'stock_cnt', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '库存重量',
                            'attribute'=>'stock_weight',
                            'filter' => Html::activeTextInput($searchModel, 'stock_weight', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '颜色',
                            'attribute' => 'stone_color',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_color);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
                            'label' => '净度',
                            'attribute' => 'stone_clarity',
                            'value' => function($model){
                                return Yii::$app->attr->valueName($model->stone_clarity);
                            },
                            'filter' => false,
                            'headerOptions' => ['width'=>'80'],
                        ],
                        [
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
                        ],
                        [
                            'label' => '每卡采购价格',
                            'attribute'=>'cost_price',
                            'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
                        ],
                        [
                            'label' => '每卡销售价格',
                            'attribute'=>'sale_price',
                            'filter' => Html::activeTextInput($searchModel, 'sale_price', [
                                'class' => 'form-control',
                            ]),
                            'headerOptions' => ['width' => '120'],
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