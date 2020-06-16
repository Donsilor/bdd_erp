<?php

use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\ConfirmEnum;
use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Supply\common\enums\BuChanEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;

$this->title = '石料采购详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?php echo $purchase->purchase_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
            if($purchase->purchase_status == \addons\Purchase\common\enums\PurchaseStatusEnum::SAVE){
                echo Html::create(['edit', 'purchase_id' => $purchase->id], '创建', [
                    'class' => 'btn btn-primary btn-xs openIframe',
                    'data-width'=>'90%',
                    'data-height'=>'90%',
                    'data-offset'=>'20px',
                ]);
            }
            if($purchase->purchase_status == BillStatusEnum::CONFIRM) {
                echo Html::batchPop(['warehouse'],'分批收货', [
                    'class'=>'btn btn-success btn-xs',
                ]);
            }
        ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-15" style="padding-left: 0px;padding-right: 0px;">
            <div class="box">
                <div class="box-body table-responsive" style="padding-left: 0px;padding-right: 0px;">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => true,//显示footer行
                        'options' => ['style'=>'white-space:nowrap;'],
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
                                    'attribute'=>'goods_name',
                                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->goods_name;
                                    },
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'300'],
                            ],
                            [
                                'attribute'=>'goods_sn',
                                'filter' => Html::activeTextInput($searchModel, 'goods_sn', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->goods_sn;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute' => 'stone_type',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return Yii::$app->attr->valueName($model->stone_type) ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                                    ],
                            [
                                    'attribute' => 'stone_num',
                                    'filter' => Html::activeTextInput($searchModel, 'stone_num', [
                                         'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->stone_num ;
                                    },
                                   'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                    'attribute' => 'stone_color',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return Yii::$app->attr->valueName($model->stone_color) ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute' => 'stone_clarity',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return Yii::$app->attr->valueName($model->stone_clarity) ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute' => 'goods_weight',
                                    'filter' => Html::activeTextInput($searchModel, 'goods_weight', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->goods_weight ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute'=>'cost_price',
                                    'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->cost_price ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute'=>'stone_price',
                                    'filter' => Html::activeTextInput($searchModel, 'stone_price', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                        return $model->stone_price ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'is_receipt',
                                'value' => function ($model){
                                    return ConfirmEnum::getValue($model->is_receipt);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'is_receipt',ConfirmEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{edit} {apply-edit} {delete}',
                                'buttons' => [ 
                                    'edit' => function($url, $model, $key) use($purchase){
                                         if($purchase->purchase_status == PurchaseStatusEnum::SAVE) {
                                             return Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }                                         
                                    },
                                    'apply-edit' =>function($url, $model, $key){
                                        
                                    },                                    
                                    'delete' => function($url, $model, $key) use($purchase){
                                        if($purchase->purchase_status == PurchaseStatusEnum::SAVE) {
                                            return Html::delete(['delete','id' => $model->id,'purchase_id'=>$purchase->id,'returnUrl' => Url::getReturnUrl()],'删除',['class' => 'btn btn-danger btn-xs']);
                                        }
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