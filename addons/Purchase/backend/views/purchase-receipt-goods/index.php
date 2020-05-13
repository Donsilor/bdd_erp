<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

$this->title = '收货单详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $receipt->receipt_no ?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                    <?php //echo Html::checkboxList('colmun','',\Yii::$app->purchaseService->purchaseGoods->listColmuns(1))?>
                    </h3>
                    <div class="box-tools">
                        <?= Html::create(['edit', 'purchase_id' => $receipt->id], '创建', [
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
                                    'headerOptions' => ['width'=>'60'],
                            ],
                            [
                                    'attribute' => 'purchase_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                    'attribute' => 'produce_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
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
                                'attribute' => 'factory_mo',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'finger',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'xiangkou',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'material',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'gold_price',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'gold_loss',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'gross_weight',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'cost_price',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'cert_id',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'main_stone',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'main_stone_num',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'main_stone_weight',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'main_stone_color',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'main_stone_clarity',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'main_stone_price',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone1',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_num1',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_price1',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_weight1',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone2',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_num2',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_weight2',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_price2',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone3',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_num3',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_weight3',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'second_stone_price3',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'fee_price',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'parts_price',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'extra_stone_fee',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'tax_fee',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'other_fee',
                                'filter' => true,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
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