<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Purchase\common\enums\ApplyStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\JintuoTypeEnum;

$this->title = '采购申请明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">采购申请详情 - <?php echo $apply->apply_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
            if($apply->apply_status == ApplyStatusEnum::SAVE){
                echo Html::create(['edit', 'apply_id' => $apply->id], '创建', [
                    'class' => 'btn btn-primary btn-xs openIframe',
                    'data-width'=>'90%',
                    'data-height'=>'90%',
                    'data-offset'=>'20px',
                ]);
            }
        ?>

    </div>
    <div class="tab-content">
        <div class="row">
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
                                    'value' => "goods_name",
                                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-2'],
                            ],                                                      
                            [
                                    'attribute' => 'style_sn',
                                    'value' =>'style_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'qiban_sn',
                                    'value' =>'qiban_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],  
                            [      
                                    'attribute' => 'qiban_type',
                                    'value' => function($model){
                                            return QibanTypeEnum::getValue($model->qiban_type);
                                     },
                                     'filter' => Html::activeDropDownList($searchModel, 'qiban_type',QibanTypeEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                   'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'jintuo_type',
                                    'value' => function($model){
                                        return JintuoTypeEnum::getValue($model->jintuo_type);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'jintuo_type',JintuoTypeEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],                            
                            [
                                    'label' => '款式分类',
                                    'attribute' => 'style_cate_id',
                                    'value' => function($model){
                                        return $model->cate->name ?? '';
                                    },
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
                                    'value' => function($model){
                                        return $model->type->name ?? '';
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
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
                                   'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                    'attribute'=>'cost_price',
                                    'value' => function ($model) {
                                        return $model->cost_price ;
                                    },
                                    'filter' => false,
                                    'headerOptions' => ['width'=>'120'],
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
                            ],                            
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{view} {edit} {apply-edit} {delete}',
                                'buttons' => [
                                    'view'=> function($url, $model, $key){
                                        return Html::edit(['view','id' => $model->id, 'apply_id'=>$model->apply_id, 'search'=>1,'returnUrl' => Url::getReturnUrl()],'详情',[
                                            'class' => 'btn btn-info btn-xs',
                                        ]);
                                    },
                                    'edit' => function($url, $model, $key) use($apply){
                                         if($apply->apply_status == ApplyStatusEnum::SAVE) {
                                             return Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }                                         
                                    },
                                    'apply-edit' =>function($url, $model, $key) use($apply){
                                        if($apply->apply_status != ApplyStatusEnum::SAVE) {
                                            return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },                                    
                                    'delete' => function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::SAVE) {
                                            return Html::delete(['delete','id' => $model->id,'apply_id'=>$apply->id,'returnUrl' => Url::getReturnUrl()],'删除',['class' => 'btn btn-danger btn-xs']);
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