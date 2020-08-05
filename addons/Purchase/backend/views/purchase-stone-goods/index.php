<?php

use addons\Style\common\enums\AttrIdEnum;
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
    <h2 class="page-header"><?php echo $this->title;?> - <?php echo $purchase->purchase_sn?> - <?php echo PurchaseStatusEnum::getValue($purchase->purchase_status);?></h2>
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
                echo Html::batchPop(['warehouse', 'check' => 1],'分批收货', [
                    'class'=>'btn btn-success btn-xs',
                    'data-width'=>'40%',
                    'data-height'=>'60%',
                    'data-offset'=>'20px',
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
                        'options' => ['style'=>' width:150%; white-space:nowrap;'],
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
                                        return $model->goods_name??"";
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
                                    return $model->goods_sn??"";
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
                                    //'filter' => Html::activeTextInput($searchModel, 'stone_num', [
                                    //     'class' => 'form-control',
                                    //]),
                                    'value' => function ($model) {
                                        return $model->stone_num??"0";
                                    },
                                    'filter' => false,
                                   'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'stone_weight',
                                //'filter' => Html::activeTextInput($searchModel, 'stone_weight', [
                                //   'class' => 'form-control',
                                //]),
                                'value' => function ($model) {
                                    return $model->stone_weight??"0.00";
                                },
                                'filter' => false,
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute' => 'goods_weight',
                                    //'filter' => Html::activeTextInput($searchModel, 'goods_weight', [
                                    //   'class' => 'form-control',
                                    //]),
                                    'value' => function ($model) {
                                        return $model->goods_weight??"0.00";
                                    },
                                    'filter' => false,
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_shape',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->stone_shape)??"";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_shape',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_color',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->stone_color) ;
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_color',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_clarity',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->stone_clarity) ;
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_clarity',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_cut',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->stone_cut);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_cut',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_symmetry',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->stone_symmetry);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_symmetry',Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_polish',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->stone_polish);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_polish',Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_fluorescence',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->stone_fluorescence);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_fluorescence',Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_colour',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->stone_colour);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_colour',Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'stone_size',
                                //'filter' => Html::activeTextInput($searchModel, 'stone_size', [
                                //    'class' => 'form-control',
                                //]),
                                'value' => function ($model) {
                                    return $model->stone_size??"";
                                },
                                'filter' => false,
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'cert_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->cert_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'cert_type',Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute'=>'cert_id',
                                'filter' => Html::activeTextInput($searchModel, 'cert_id', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->cert_id??"";
                                },
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute'=>'stone_price',
                                //'filter' => Html::activeTextInput($searchModel, 'stone_price', [
                                //   'class' => 'form-control',
                                //]),
                                'value' => function ($model) {
                                    return $model->stone_price??"0.00";
                                },
                                'filter' => false,
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute'=>'cost_price',
                                //'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                //        'class' => 'form-control',
                                //]),
                                'value' => function ($model) {
                                    return $model->cost_price??"0.00";
                                },
                                'filter' => false,
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'channel_id',
                                'value' => function ($model){
                                    return $model->saleChannel->name??"";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'channel_id', \Yii::$app->salesService->saleChannel->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style' => 'width:100px;',
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'spec_remark',
                                'filter' => Html::activeTextInput($searchModel, 'spec_remark', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->spec_remark??"";
                                },
                                'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                'attribute' => 'remark',
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->remark??"";
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
                                'contentOptions' => ['style' => ['white-space' => 'nowrap']],
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