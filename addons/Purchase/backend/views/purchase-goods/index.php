<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Supply\common\enums\BuChanEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use addons\Style\common\enums\AttrIdEnum;

$this->title = '采购商品';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">采购详情 - <?php echo $purchase->purchase_sn?></h2>
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
        ?>

    </div>
    <div class="tab-content">
        <div class="row col-xs-15">
            <div class="box">
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'options' => ['style'=>'width:180%;white-space:nowrap;'],
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
                                    'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{view} {edit} {apply-edit} {print_edit} {delete}',
                                'buttons' => [
                                    'view'=> function($url, $model, $key){
                                        return Html::edit(['view','id' => $model->id, 'purchase_id'=>$model->purchase_id, 'search'=>1,'returnUrl' => Url::getReturnUrl()],'详情',[
                                            'class' => 'btn btn-info btn-xs',
                                        ]);
                                    },
                                    'edit' => function($url, $model, $key) use($purchase){
                                         if($purchase->purchase_status == PurchaseStatusEnum::SAVE) {
                                             return Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }
                                    },
                                    'print_edit' => function($url, $model, $key) use($purchase){
                                        return Html::edit(['purchase-goods-print/edit','purchase_goods_id' => $model->id],'制造单打印编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                    },
                                    'apply-edit' =>function($url, $model, $key) use($purchase){
                                        if(($purchase->purchase_status != PurchaseStatusEnum::SAVE) && (!$model->produce || $model->produce->bc_status < BuChanEnum::IN_PRODUCTION)) {
                                            return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($purchase){
                                        if($purchase->purchase_status == PurchaseStatusEnum::SAVE) {
                                            return Html::delete(['delete','id' => $model->id,'purchase_id'=>$purchase->id,'returnUrl' => Url::getReturnUrl()],'删除',['class' => 'btn btn-danger btn-xs']);
                                        }
                                    },
                                ]
                           ],
                            [
                                'label' => '商品图片',
                                'value' => function ($model) {
                                    return \common\helpers\ImageHelper::fancyBox(Yii::$app->purchaseService->purchaseGoods->getStyleImage($model),90,90);
                                },
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'90'],
                            ],
                            [
                                'attribute'=>'goods_name',
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                        'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->goods_name;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],                                                      
                            [
                                    'attribute' => 'style_sn',
                                    'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                            'class' => 'form-control',
                                            'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'qiban_sn',
                                    'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                            'class' => 'form-control',
                                            'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],  
                            [      
                                    'attribute' => 'qiban_type',
                                    'value' => function($model){
                                        return addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                                     },
                                     'filter' => Html::activeDropDownList($searchModel, 'qiban_type',addons\Style\common\enums\QibanTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'jintuo_type',
                                    'value' => function($model){
                                        return addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'peiliao_type',
                                    'value' => function($model){
                                        return \addons\Supply\common\enums\PeiliaoTypeEnum::getValue($model->peiliao_type);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'peiliao_type',\addons\Supply\common\enums\PeiliaoTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'style_cate_id',
                                    'value' => function ($model){
                                           return $model->cate->name ??'';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'product_type_id',
                                    'value' => function($model){
                                        return $model->type->name ?? '';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                            'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'style_channel_id',
                                    'value' => function($model){
                                        return $model->channel->name ?? '';
                                     },
                                    'filter' => Html::activeDropDownList($searchModel, 'style_channel_id',Yii::$app->styleService->styleChannel->getDropDown(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style'=>'width:100px'
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'goods_num',
                                    'value' => "goods_num",
                                    'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                         'class' => 'form-control',
                                         'style'=>'width:80px'
                                    ]),
                                   'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                    'label'=>'成色',
                                    'value'=> function($model){
                                        return $model->attr[AttrIdEnum::MATERIAL] ?? "";
                                    }
                            ],
                            [
                                'label'=>'金重',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::JINZHONG] ?? "";
                                }
                            ],
                            [
                                'label'=>'手寸',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::FINGER] ?? "";
                                }
                            ],
                            [
                                'label'=>'链长',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::CHAIN_LENGTH] ?? "";
                                }
                            ],
                            [
                                'label'=>'镶口',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::XIANGKOU] ?? "";
                                }
                            ],
                            [
                                'label'=>'主石类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_TYPE] ?? "";
                                }
                            ],
                            [
                                'label'=>'主石数量',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_NUM] ?? "";
                                }
                            ],
                            [
                                'label'=>'主石规格(颜色/净度/切工/抛光/对称/荧光)',
                                'value'=> function($model){
                                    $color = $model->attr[AttrIdEnum::DIA_COLOR] ?? "无";
                                    $clarity = $model->attr[AttrIdEnum::DIA_CLARITY] ?? "无";
                                    $cut = $model->attr[AttrIdEnum::DIA_CUT] ?? "无";
                                    $polish = $model->attr[AttrIdEnum::DIA_POLISH] ?? "无";
                                    $symmetry = $model->attr[AttrIdEnum::DIA_SYMMETRY] ?? "无";
                                    $fluorescence = $model->attr[AttrIdEnum::DIA_FLUORESCENCE] ?? "无";
                                    return $color.'/'.$clarity.'/'.$cut.'/'.$polish.'/'.$symmetry.'/'.$fluorescence;
                                }
                            ],
                            [
                                'label'=>'副石1类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_TYPE] ?? "";
                                }
                            ],
                            [
                                'label'=>' 副石1数量',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_NUM] ?? "";
                                }
                            ],
                            [
                                'label'=>'副石1规格(颜色/净度)',
                                'value'=> function($model){
                                    $color = $model->attr[AttrIdEnum::SIDE_STONE1_COLOR] ?? "无";
                                    $clarity = $model->attr[AttrIdEnum::SIDE_STONE1_CLARITY] ?? "无";
                                    return $color.'/'.$clarity;
                                }
                            ],
                            [
                                'label'=>'副石2类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_TYPE] ?? "";
                                }
                            ],
                            [
                                'label'=>' 副石2数量',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_NUM] ?? "";
                                }
                            ],
                            [
                                'label'=>' 证书类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::DIA_CERT_TYPE] ?? "";
                                }
                            ],
                            [
                                'label'=>' 证书编号',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::DIA_CERT_NO] ?? "";
                                }
                            ],
                            [
                                    'attribute'=>'成本价',
                                    'filter' => Html::activeTextInput($searchModel, 'cost_price', [
                                            'class' => 'form-control',
                                            'style'=>'width:80px'
                                    ]),
                                    'value' => function ($model) {
                                        return $model->cost_price ;
                                    },
                            ],
                            [
                                    'attribute' => '申请修改',
                                    'value' => function ($model) {
                                        if($model->is_apply == common\enums\ConfirmEnum::YES) {
                                            return '已申请<br/>'.Html::edit(['apply-view','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'查看审批',[
                                                    'class' => 'btn btn-danger btn-xs',
                                            ]);
                                        }else{
                                            return '未申请';
                                        }
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'is_apply',common\enums\ConfirmEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                                    'visible'=> $purchase->purchase_status != PurchaseStatusEnum::SAVE,
                            ],
                            [
                                    'attribute' => '布产号',                                    
                                    'value' => function ($model) {
                                           if($model->produce_id && $model->produce) {
                                               return $model->produce->produce_sn ;
                                           }
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '150'],
                            ],
                            [
                                    'attribute' => '商品状态',
                                    'value' => function ($model) {
                                        if($model->produce_id && $model->produce) {
                                            return BuChanEnum::getValue($model->produce->bc_status);
                                        }else{
                                            return '未布产';
                                        }
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width' => '150'],                                    
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{view} {edit} {apply-edit} {delete}',
                                'buttons' => [
                                    'view'=> function($url, $model, $key){
                                        return Html::edit(['view','id' => $model->id, 'purchase_id'=>$model->purchase_id, 'search'=>1,'returnUrl' => Url::getReturnUrl()],'详情',[
                                            'class' => 'btn btn-info btn-xs',
                                        ]);
                                    },
                                    'edit' => function($url, $model, $key) use($purchase){
                                         if($purchase->purchase_status == PurchaseStatusEnum::SAVE) {
                                             return Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }                                         
                                    },
                                    'apply-edit' =>function($url, $model, $key) use($purchase){
                                        if(($purchase->purchase_status != PurchaseStatusEnum::SAVE) && (!$model->produce || $model->produce->bc_status < BuChanEnum::IN_PRODUCTION)) {
                                            return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
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