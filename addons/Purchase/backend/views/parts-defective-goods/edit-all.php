<?php


use common\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $defective yii\data\ActiveDataProvider */

$this->title = Yii::t('parts_defective_goods', '配件不良返厂单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $defective->defective_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($defective->defective_status == \addons\Purchase\common\enums\DefectiveStatusEnum::SAVE) {
            echo Html::a('返回列表', ['parts-defective-goods/index', 'defective_id' => $defective->id], ['class' => 'btn btn-white btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
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

                            ],
                            /*[
                                'attribute'=>'id',
                                'headerOptions' => [],
                                'filter' => Html::activeTextInput($searchModel, 'id', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                            ],*/
                            [
                                'attribute'=>'xuhao',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'xuhao', [
                                    'class' => 'form-control',
                                    'style'=> 'width:140px;'
                                ]),
                            ],
                            [
                                'attribute' => 'parts_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->parts_type);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model){
                                    return \addons\Style\common\enums\MaterialTypeEnum::getValue($model->material_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',\addons\Style\common\enums\MaterialTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute' => 'goods_color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->goods_color);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods_shape',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->goods_shape);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_size',
                                'filter' => Html::activeTextInput($searchModel, 'goods_size', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    $str = $model->goods_size;
                                    return $str;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'chain_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->chain_type);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'cramp_ring',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->cramp_ring);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_weight',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                            ],
                            [
                                'attribute'=>'goods_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'goods_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                            ],
                            [
                                'attribute'=>'cost_price',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute' => 'iqc_reason',
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxSelect($model,'iqc_reason', Yii::$app->purchaseService->fqc->getDropDown(), ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'iqc_reason', Yii::$app->purchaseService->fqc->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'iqc_remark',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model, $key, $index, $column){
                                    return  Html::ajaxInput('iqc_remark', $model->iqc_remark, ['data-id'=>$model->id]);
                                },
                                'filter' => Html::activeTextInput($searchModel, 'iqc_remark', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($defective){
                                        if($defective->audit_status == \common\enums\AuditStatusEnum::PENDING){
                                            return Html::delete(['delete', 'id' => $model->id]);
                                        }
                                    },
                                ],
                                'headerOptions' => [],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>