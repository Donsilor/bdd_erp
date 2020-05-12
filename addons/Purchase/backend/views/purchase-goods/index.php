<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;

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
                    <h3 class="box-title">
                    <?= Html::encode($this->title) ?>
                    <?php //echo Html::checkboxList('colmun','',\Yii::$app->purchaseService->purchaseGoods->listColmuns(1))?>
                    </h3>
                    <div class="box-tools">
                        <?= Html::create(['edit', 'purchase_id' => $purchase->id], '创建', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width'=>'90%',
                            'data-height'=>'90%',                            
                            'data-offset'=>'20px',
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
                                    'attribute' => 'goods_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],
                            ],                            
                            [
                                    'attribute' => 'style_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'label' => '商品类型',
                                    'attribute' => 'goods_type',
                                    'value' => function($model){
                                            return PurchaseGoodsTypeEnum::getValue($model->goods_type);
                                     },
                                    'filter' => Html::activeDropDownList($searchModel, 'goods_type',PurchaseGoodsTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],

                            [
                                    'attribute'=>'goods_name',
                                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                         $str = $model->goods_name;
                                         //$str .= "<br/>颜色:FI, 净度：VSS, 石重:0.5CT, 证书类型:GIA";
                                         return $str;
                                    },
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'300'],
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
                                    'attribute' => 'goods_num',
                                    'value' => "goods_num",
                                    'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                         'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->goods_num ;
                                    },
                                        'headerOptions' => ['width'=>'120'],
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
                                     return Html::edit([
                                             'edit','id' => $model->id,
                                             'returnUrl' => Url::getReturnUrl()],
                                             '编辑',
                                             ['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px',
                                           ]);
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