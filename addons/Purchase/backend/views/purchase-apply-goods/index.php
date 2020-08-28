<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Purchase\common\enums\ApplyStatusEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use common\enums\AuditStatusEnum;

$this->title = '采购申请明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">采购申请详情 - <?php echo $apply->apply_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php if($apply->apply_status == ApplyStatusEnum::SAVE){ ?>        
                <?= Html::create(['edit', 'apply_id' => $apply->id], '有款添加', [
                    'class' => 'btn btn-primary btn-xs openIframe',
                    'data-width'=>'90%',
                    'data-height'=>'90%',
                    'data-offset'=>'20px',
                ]);?>
                <?= Html::create(['edit-no-style', 'apply_id' => $apply->id], '无款添加', [
                    'class' => 'btn btn-primary btn-xs openIframe',
                    'data-width'=>'90%',
                    'data-height'=>'90%',
                    'data-offset'=>'20px',
                ]);?>
                <?= Html::create(['edit-diamond', 'apply_id' => $apply->id], '裸钻添加', [
                    'class' => 'btn btn-primary btn-xs openIframe',
                    'data-width'=>'90%',
                    'data-height'=>'90%',
                    'data-offset'=>'20px',
                ]);?>
        <?php         
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
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{edit} {apply-edit} {format-edit} {audit}   {delete}',
                                'buttons' => [
                                    'edit' => function($url, $model, $key) use($apply){
                                         if($apply->apply_status <= ApplyStatusEnum::CONFIRM ) {
                                             if($model->product_type_id == 1){
                                                 $action = 'edit-diamond';
                                             }else{
                                                 $action = ($model->goods_type == PurchaseGoodsTypeEnum::OTHER) ? 'edit-no-style' :'edit';
                                             }
                                             return Html::edit([$action,'id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }
                                    },
                                    'format-edit' =>function($url, $model, $key) use($apply){
                                        if($apply->apply_status <= ApplyStatusEnum::CONFIRM  && $model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::DESIGN) {
                                            return Html::edit(['format-edit','id' => $model->id],'版式编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },
                                    'apply-edit' =>function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::AUDITED && $model->is_apply == \common\enums\ConfirmEnum::NO) {
                                            return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },

                                    'audit' => function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::CONFIRM){
                                            if($model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::DESIGN){
                                                return Html::edit(['design-confirm','id'=>$model->id], '设计部确认', [
                                                    'class'=>'btn btn-success btn-xs',
                                                    'onclick' => 'rfTwiceAffirm(this,"提交确认", "确定确认吗？");return false;',
                                                ]);
                                            }elseif ($model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::GOODS){
                                                return Html::edit(['goods-confirm','id'=>$model->id], '商品部确认', [
                                                    'class'=>'btn btn-success btn-xs',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#ajaxModal',
                                                ]);
                                            }

                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::SAVE) {
                                            return Html::delete(['delete','id' => $model->id,'apply_id'=>$apply->id,'returnUrl' => Url::getReturnUrl()],'删除',['class' => 'btn btn-danger btn-xs']);
                                        }
                                    },
                                ]
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
                                'visible' => $apply->apply_status != ApplyStatusEnum::SAVE,
                            ],
                            [
                                    'label' => '商品图片',
                                    'value' => function ($model) {
                                        return \common\helpers\ImageHelper::fancyBox($model->goods_image,90,90);
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'90'],
                            ],
                            [
                                    'attribute'=>'goods_name',                                
                                    'value' => function($model){
                                        return Html::a($model->goods_name, ['view', 'id' => $model->id, 'apply_id'=>$model->apply_id, 'search'=>1,'returnUrl' => Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                                    },
                                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
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
                                    'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                    'filter' => false,
                                    'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                    'label'=>'材质',
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
                                    'format' => 'raw',
                                    'value'=> function($model){
                                        $str = '';
                                        if(isset($model->attr[AttrIdEnum::FINGER])){
                                            $str .= $model->attr[AttrIdEnum::FINGER]."/美号";
                                            $str .= "<br/>";
                                        }
                                        if(isset($model->attr[AttrIdEnum::PORT_NO])){
                                            $str .= $model->attr[AttrIdEnum::PORT_NO]."/港号";
                                        }
                                        return $str;
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
                            'format_sn',
                            [
                                'attribute' => 'supplier_id',
                                'value' =>"supplier.supplier_name",
                                'filter'=>\kartik\select2\Select2::widget([
                                    'name'=>'SearchModel[supplier_id]',
                                    'value'=>$searchModel->supplier_id,
                                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                                    'options' => ['placeholder' =>"请选择",'style'=>"width:180px"],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],


                            [
                                'attribute' => 'confirm_status',
                                'value' => function ($model){
                                    return \addons\Purchase\common\enums\ApplyConfirmEnum::getValue($model->confirm_status);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'confirm_status',\addons\Purchase\common\enums\ApplyConfirmEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                'attribute' => 'confirm_design_id',
                                'value' => "designMember.username",
                                'filter' => Html::activeTextInput($searchModel, 'designMember.username', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'confirm_design_time',
                                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'confirm_design_time',
                                    'value' => $searchModel->confirm_design_time,
                                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd',
                                        'locale' => [
                                            'separator' => '/',
                                        ],
                                        'endDate' => date('Y-m-d',time()),
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'todayBtn' => 'linked',
                                        'clearBtn' => true,


                                    ],

                                ]),
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->confirm_design_time);
                                }

                            ],
                            [
                                'attribute' => 'confirm_goods_id',
                                'value' => "goodsMember.username",
                                'filter' => Html::activeTextInput($searchModel, 'designMember.username', [
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'80'],
                            ],

                            [
                                'attribute'=>'confirm_goods_time',
                                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                                    'model' => $searchModel,
                                    'attribute' => 'confirm_goods_time',
                                    'value' => $searchModel->confirm_goods_time,
                                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:150px;'],
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd',
                                        'locale' => [
                                            'separator' => '/',
                                        ],
                                        'endDate' => date('Y-m-d',time()),
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'todayBtn' => 'linked',
                                        'clearBtn' => true,


                                    ],

                                ]),
                                'value'=>function($model){
                                    return Yii::$app->formatter->asDatetime($model->confirm_goods_time);
                                }

                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{view} {edit} {apply-edit} {format-edit} {audit}   {delete}',
                                'buttons' => [
                                    'view'=> function($url, $model, $key){
                                        return Html::edit(['view','id' => $model->id, 'apply_id'=>$model->apply_id, 'search'=>1,'returnUrl' => Url::getReturnUrl()],'详情',[
                                            'class' => 'btn btn-info btn-xs',
                                        ]);
                                    },
                                    'edit' => function($url, $model, $key) use($apply){
                                        if($apply->apply_status <= ApplyStatusEnum::CONFIRM ) {
                                            if($model->product_type_id == 1){
                                                $action = 'edit-diamond';
                                            }else{
                                                $action = ($model->goods_type == PurchaseGoodsTypeEnum::OTHER) ? 'edit-no-style' :'edit';
                                            }
                                            return Html::edit([$action,'id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },
                                    'format-edit' =>function($url, $model, $key) use($apply){
                                        if($apply->apply_status <= ApplyStatusEnum::CONFIRM  && $model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::DESIGN) {
                                            return Html::edit(['format-edit','id' => $model->id],'版式编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },
                                    'apply-edit' =>function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::AUDITED && $model->is_apply == \common\enums\ConfirmEnum::NO) {
                                            return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },

                                    'audit' => function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::CONFIRM){
                                            if($model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::DESIGN){
                                                return Html::edit(['design-confirm','id'=>$model->id], '设计部确认', [
                                                    'class'=>'btn btn-success btn-xs',
                                                    'onclick' => 'rfTwiceAffirm(this,"提交确认", "确定确认吗？");return false;',
                                                ]);
                                            }elseif ($model->confirm_status == \addons\Purchase\common\enums\ApplyConfirmEnum::GOODS){
                                                return Html::edit(['goods-confirm','id'=>$model->id], '商品部确认', [
                                                    'class'=>'btn btn-success btn-xs',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#ajaxModal',
                                                ]);
                                            }

                                        }
                                    },
                                    'delete' => function($url, $model, $key) use($apply){
                                        if($apply->apply_status == ApplyStatusEnum::SAVE) {
                                            return Html::delete(['delete','id' => $model->id,'apply_id'=>$apply->id,'returnUrl' => Url::getReturnUrl()],'删除',['class' => 'btn btn-danger btn-xs']);
                                        }
                                    },
                                ]
                            ],
                      ]
                    ]); ?>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>