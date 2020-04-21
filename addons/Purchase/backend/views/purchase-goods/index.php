<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

$this->title = '采购商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">采购详情 - <?php echo $purchase->purchase_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools">
                        <?= Html::create(['ajax-edit', 'purchase' => $purchase->id,'returnUrl' => Url::getReturnUrl()], '创建', [
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModalLg',
                        ]); ?>
                    </div>
                </div>
            <div class="box-body table-responsive">  
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => true,//显示footer行
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
                                    'headerOptions' => ['width'=>'100'],
                            ],            
                            [
                                    'attribute' => 'style_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],            ],
                            [
                                    'attribute'=>'goods_name',
                                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                         return $model->goods_name;
                                    },
                                    'headerOptions' => ['width'=>'300'],
                            ],
                            [
                                    'label' => '商品类型',
                                    'attribute' => 'style_cate_id',
                                    'value' => "cate.name",
                                    'filter' => Html::activeDropDownList($searchModel, 'purchase_type',Yii::$app->styleService->styleCate->getDropDown(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'label' => '款式分类',
                                    'attribute' => 'style_cate_id',
                                    'value' => "cate.name",
                                    'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'label' => '产品线',
                                    'attribute' => 'product_type_id',
                                    'value' => "type.name",
                                    'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],            
                            [
                                    'attribute'=>'成本价',
                                    'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->cost_price ;
                                    },
                                    'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                'attribute' => 'status',                
                                'value' => function ($model){
                                    return \common\enums\StatusEnum::getValue($model->status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'status',\common\enums\StatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{edit}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key){
                                        return Html::edit(['edit-all','style_id' => $model->style_id,'returnUrl' => Url::getReturnUrl()]);
                                    },
                                    'status' => function($url, $model, $key){
                                            return Html::status($model['status']);
                                    },
                                ]
                           ]
                      ]
                    ]); ?>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>