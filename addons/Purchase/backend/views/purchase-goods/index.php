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
        <?= Html::button('打印', [
            'class'=>'btn btn-success btn-xs',
            'onclick' => 'batchPrint()',
        ]);?>

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
                                'attribute'=>'style_sn',
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'qiban_sn',
                                'filter' => Html::activeTextInput($searchModel, 'qiban_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:150px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'style_cate_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => 'cate.name',
                                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'

                                ]),
                            ],
                            [
                                'attribute' => 'product_type_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function($model){
                                    return $model->productType->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType::getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'

                                ]),
                            ],



                            [
                                'attribute'=>'goods_name',
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return  $model->goods_name;
                                },
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                    'style'=> 'width:200px;'
                                ]),
                                'headerOptions' => [],
                            ],


                            [
                                'attribute' => 'peishi_type',
                                'value' => function($model){
                                    return \addons\Supply\common\enums\PeishiTypeEnum::getValue($model->peishi_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peishi_type',\addons\Supply\common\enums\PeishiTypeEnum::getMap(), [
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
                                'attribute' => 'peijian_type',
                                'value' => function($model){
                                    $button = "";
                                    if($model->peijian_type>1){
                                        $button = Html::edit(['ajax-parts','id' => $model->id,'returnUrl' => Url::getReturnUrl()],'编辑配件',[
                                            'class' => 'btn btn-primary btn-xs',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#ajaxModalLg',
                                        ]);
                                    }
                                    return \addons\Supply\common\enums\PeijianTypeEnum::getValue($model->peijian_type).$button;
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peijian_type',\addons\Supply\common\enums\PeijianTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=>'width:100px'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'templet_type',
                                'value' => function($model){
                                    return \addons\Supply\common\enums\TempletTypeEnum::getValue($model->templet_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'templet_type',\addons\Supply\common\enums\TempletTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=>'width:100px'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'label'=>'材质',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MATERIAL] ?? "";
                                }
                            ],
                            [
                                'label'=>'金料颜色',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MATERIAL_COLOR] ?? "";
                                }
                            ],

                            [
                                'attribute'=>'goods_num',
                                'filter' => Html::activeTextInput($searchModel, 'goods_num', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'手寸（港）',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::PORT_NO] ?? "";
                                }
                            ],
                            [
                                'label'=>'手寸（美）',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::FINGER] ?? "";
                                }
                            ],

                            [
                                'label'=>'尺寸',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::CHAIN_LENGTH] ?? "";
                                }
                            ],
                            [
                                'label'=>'成品尺寸',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::PRODUCT_SIZE] ?? "";
                                }
                            ],
                            [
                                'label'=>'镶口',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::XIANGKOU] ?? "";
                                }
                            ],
                            [
                                'label'=>'刻字',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::KEZI] ?? "";
                                }
                            ],
                            [
                                'label'=>'链类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::CHAIN_TYPE] ?? "";
                                }
                            ],
                            [
                                'label'=>'扣环',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::CHAIN_BUCKLE] ?? "";
                                }
                            ],
                            [
                                'label'=>'爪头形状',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::TALON_HEAD_TYPE] ?? "";
                                }
                            ],
                            [
                                'attribute' => 'peiliao_way',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getValue($model->peiliao_way);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peiliao_way',\addons\Warehouse\common\enums\PeiLiaoWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],


                            [
                                'label'=>'金重(g)',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::JINZHONG] ?? "";
                                }
                            ],

                            [
                                'attribute'=>'suttle_weight',
                                'filter' => Html::activeTextInput($searchModel, 'suttle_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'gold_loss',
                                'filter' => Html::activeTextInput($searchModel, 'gold_loss', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'gross_weight',
                                'filter' => Html::activeTextInput($searchModel, 'gross_weight', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'gold_price',
                                'filter' => Html::activeTextInput($searchModel, 'gold_price', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],

                            [
                                'attribute'=>'gold_amount',
                                'filter' => Html::activeTextInput($searchModel, 'gold_amount', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],

                            /***主石信息开始**/
                            [
                                'attribute' => 'peishi_type',
                                'value' => function($model){
                                    return \addons\Supply\common\enums\PeishiTypeEnum::getValue($model->peishi_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peishi_type',\addons\Supply\common\enums\PeishiTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],

                            [
                                'attribute'=>'main_peishi_way',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->main_peishi_way);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_peishi_way',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'主石类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_TYPE] ?? "";
                                }
                            ],
                            [
                                'attribute'=>'main_stone_sn',
                                'filter' => Html::activeTextInput($searchModel, 'main_stone_sn', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'主石粒数',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_NUM] ?? "";
                                }
                            ],
                            [
                                'label'=>'主石形状',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_SHAPE] ?? "";
                                }
                            ],
                            [
                                'label'=>'	主石大小',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_WEIGHT] ?? "";
                                }
                            ],
                            [
                                'label'=>'	主石单价',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::MAIN_STONE_PRICE] ?? "";
                                }
                            ],
                            [
                                'label'=>'主石成本',
                                'value' => function($model){
                                    $main_stone_price = $model->attr[AttrIdEnum::MAIN_STONE_PRICE] ?? 0;
                                    $main_stone_weight = $model->attr[AttrIdEnum::MAIN_STONE_WEIGHT] ?? 0;
                                    return round($main_stone_weight * $main_stone_price,2);
                                },
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'主石规格(颜色/净度/切工/色彩)',
                                'value'=> function($model){
                                    $color = $model->attr[AttrIdEnum::DIA_COLOR] ?? "无";
                                    $clarity = $model->attr[AttrIdEnum::DIA_CLARITY] ?? "无";
                                    $cut = $model->attr[AttrIdEnum::DIA_CUT] ?? "无";
                                    $colour = $model->attr[AttrIdEnum::MAIN_STONE_COLOUR] ?? "无";

                                    return $color.'/'.$clarity.'/'.$cut.'/'.$colour.'/';
                                }
                            ],

                            /***副石1开始**/

                            [
                                'attribute'=>'second_peishi_way1',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way1);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way1',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'	副石1类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_TYPE] ?? "";
                                }
                            ],
                            [
                                'attribute'=>'second_stone_sn1',
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn1', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'	副石1粒数',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_NUM] ?? "";
                                }
                            ],
                            [
                                'label'=>'	副石1形状',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_SHAPE] ?? "";
                                }
                            ],
                            [
                                'label'=>'	副石1重',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? "";
                                }
                            ],
                            [
                                'label'=>'	副石1单价',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE1_PRICE] ?? "";
                                }
                            ],
                            [
                                'label'=>'副石1成本',
                                'value' => function($model){
                                    $side_stone1_price = $model->attr[AttrIdEnum::SIDE_STONE1_PRICE] ?? 0;
                                    $side_stone1_weight = $model->attr[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? 0;
                                    $side_stone1_weight = $side_stone1_weight == ''? 0:$side_stone1_weight;
                                    return round($side_stone1_weight * $side_stone1_price,2);
                                },
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'副石1规格(颜色/净度/色彩)',
                                'value'=> function($model){
                                    $color = $model->attr[AttrIdEnum::SIDE_STONE1_COLOR] ?? "无";
                                    $clarity = $model->attr[AttrIdEnum::SIDE_STONE1_CLARITY] ?? "无";
                                    $colour = $model->attr[AttrIdEnum::SIDE_STONE1_COLOUR] ?? "无";
                                    return $color.'/'.$clarity.'/'.$colour;
                                }
                            ],
                            /***副石2开始**/

                            [
                                'attribute'=>'second_peishi_way2',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->second_peishi_way2);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'second_peishi_way2',\addons\Warehouse\common\enums\PeiShiWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],


                            [
                                'label'=>'	副石2类型',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_TYPE] ?? "";
                                }
                            ],
                            [
                                'attribute'=>'second_stone_sn2',
                                'filter' => Html::activeTextInput($searchModel, 'second_stone_sn2', [
                                    'class' => 'form-control',
                                    'style'=> 'width:60px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'	副石2粒数',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_NUM] ?? "";
                                }
                            ],
                            [
                                'label'=>'	副石2形状',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_SHAPE] ?? "";
                                }
                            ],
                            [
                                'label'=>'	副石2重',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_WEIGHT] ?? "";
                                }
                            ],
                            [
                                'label'=>'	副石2单价',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::SIDE_STONE2_PRICE] ?? "";
                                }
                            ],
                            [
                                'label'=>'副石2成本',
                                'value' => function($model){
                                    $side_stone2_price = $model->attr[AttrIdEnum::SIDE_STONE2_PRICE] ?? 0;
                                    $side_stone2_weight = $model->attr[AttrIdEnum::SIDE_STONE2_WEIGHT] ?? 0;
                                    $side_stone2_weight = $side_stone2_weight == ''? 0:$side_stone2_weight;
                                    return round($side_stone2_weight * $side_stone2_price,2);
                                },
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'副石2规格(颜色/净度/色彩)',
                                'value'=> function($model){
                                    $color = $model->attr[AttrIdEnum::SIDE_STONE2_COLOR] ?? "无";
                                    $clarity = $model->attr[AttrIdEnum::SIDE_STONE2_CLARITY] ?? "无";
                                    $colour = $model->attr[AttrIdEnum::SIDE_STONE2_COLOUR] ?? "无";
                                    return $color.'/'.$clarity.'/'.$colour;
                                }
                            ],
                            /**副石2结束**/

                            [
                                'attribute'=>'stone_info',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'peijian_way',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\PeiShiWayEnum::getValue($model->peijian_way);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peijian_way',\addons\Warehouse\common\enums\PeiJianWayEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'peijian_cate',
                                'value' => function($model){
                                    return \addons\Warehouse\common\enums\PeiJianCateEnum::getValue($model->peijian_cate);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'peijian_cate',\addons\Warehouse\common\enums\PeiJianCateEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'parts_material',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'parts_num',
                                'filter' => false,
                                'headerOptions' => [],
                            ],

                            [
                                'attribute'=>'parts_weight',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'parts_price',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'配件额',
                                'value' => function($model){
                                    return round($model->parts_weight * $model->parts_price,2);
                                },
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'parts_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],

                            [
                                'attribute'=>'peishi_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'peishi_amount',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'ke_gong_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'gong_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'total_gong_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'xianqian_price',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'xiangqian_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],

                            [
                                'label'=>'表面工艺',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::FACEWORK] ?? "";
                                }
                            ],
                            [
                                'attribute'=>'biaomiangongyi_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'fense_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'penrasa_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'bukou_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'edition_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'cert_fee',
                                'filter' => false,
                                'headerOptions' => [],
                            ],

                            [
                                'attribute'=>'factory_cost_price',
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                                'headerOptions' => [],
                            ],
                            [
                                'label'=>'主石证书类型	',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::DIA_CERT_TYPE] ?? "";
                                }
                            ],

                            [
                                'label'=>'主石证书号	',
                                'value'=> function($model){
                                    return $model->attr[AttrIdEnum::DIA_CERT_NO] ?? "";
                                }
                            ],


                            [
                                'attribute' => 'style_channel_id',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function($model){
                                    return $model->channel->name ?? '';
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_channel_id',Yii::$app->salesService->saleChannel->getDropDown(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:120px;'

                                ]),
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
                                'attribute' => 'style_sex',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                            ],
                            [
                                'attribute'=>'qiban_type',
                                'value'=> function($model){
                                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'

                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'jintuo_type',
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'value' => function ($model){
                                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
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
<script>
    function batchPrint() {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        if(ids.length == 0){
            rfMsg('请选中打印项')
        }
        window.open("<?= Url::buildUrl('../purchase-goods-print/print',[],['id'])?>?id=" + ids,'_blank');
    }

</script>