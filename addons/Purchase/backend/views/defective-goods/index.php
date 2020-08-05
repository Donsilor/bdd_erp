<?php


use addons\Purchase\common\enums\DefectiveStatusEnum;
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

$this->title = Yii::t('defective_goods', '不良返厂单详情');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $defective->defective_no?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($defective->defective_status == \addons\Purchase\common\enums\DefectiveStatusEnum::SAVE) {
            echo Html::create(['add', 'defective_id' => $defective->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
            ]);
            echo '&nbsp;&nbsp;&nbsp;';
            echo Html::edit(['edit-all', 'defective_id' => $defective->id], '编辑货品', ['class'=>'btn btn-info btn-xs']);
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
                                'attribute'=>'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'attribute'=>'factory_mo',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => Html::activeTextInput($searchModel, 'factory_mo', [
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'
                                ]),
                            ],
                            [
                                'label' => '质检未过原因',
                                'attribute' => 'fqc.name',
                                'value' => "fqc.name",
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
                                'filter' => Html::activeTextInput($searchModel, 'iqc_remark', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                            ],
                            [
                                'label' => '款式分类',
                                'attribute' => 'cate.name',
                                'value' => "cate.name",
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id', \Yii::$app->styleService->styleCate->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label' => '产品线',
                                'attribute' => 'type.name',
                                'value' => "type.name",
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            /*[
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($defective){
                                        if($defective->defective_status == DefectiveStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id],'删除',[
                                                'class'=>'btn btn-danger btn-xs',
                                            ]);
                                        }
                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-1'],
                            ]*/
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- box end -->
    </div>
    <!-- tab-content end -->
</div>