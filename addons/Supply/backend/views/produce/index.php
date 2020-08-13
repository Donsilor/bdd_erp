<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

use addons\Supply\common\enums\BuChanEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Supply\common\enums\PeishiTypeEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '布产列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                </div>
            </div>
            <div class="box-body table-responsive" style="white-space:nowrap;">
    <?php //echo Html::batchButtons()?>                  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'options' => ['style'=>'overflow-x: scroll;'],
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
                    'headerOptions' => ['width'=>'30'],
            ],
            [
                    'attribute' => 'id',
                    'filter' => true,
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],  
            [
                    'attribute' => 'produce_sn',
                    'value'=>function($model) {
                        return Html::a($model->produce_sn, ['view', 'id' => $model->id,'returnUrl'=>Url::getReturnUrl()], ['style'=>"text-decoration:underline;color:#3c8dbc"]);
                    },
                    'filter' => Html::activeTextInput($searchModel, 'produce_sn', [
                            'class' => 'form-control',
                            'style' => 'width:150px;',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'130'],
            ],
            [
                'attribute' => 'supplier_id',
                'value' => function($model){
                    return $model->supplier->supplier_name ?? '';
                },
                'filter'=>\kartik\select2\Select2::widget([
                    'name'=>'SearchModel[supplier_id]',
                    'value'=>$searchModel->supplier_id,
                    'data'=>Yii::$app->supplyService->supplier->getDropDown(),
                    'options' => ['placeholder' =>"请选择",'class' => 'col-md-1','style' => 'width:150px;',],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'format' => 'raw',
                'headerOptions' => ['class' =>'col-md-1'],
            ],
            [
                    'attribute' => 'from_type',
                    'value' => function ($model){
                        return \addons\Supply\common\enums\FromTypeEnum::getValue($model->from_type);
                    },
                    'filter' =>Html::activeDropDownList($searchModel, 'from_type',\addons\Supply\common\enums\FromTypeEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                        'style' => 'width:80px;',
                    ]),
                    'format' => 'raw',
            ],
            [
                    'attribute' => 'purchase_sn',
                    'value'=>function($model){
                        if($model->from_type == \addons\Supply\common\enums\FromTypeEnum::ORDER){
                            return $model->purchase_sn."({$model->order_sn})";
                        }else{
                            return $model->purchase_sn;
                        }
                    },
                    'filter' => Html::activeTextInput($searchModel, 'purchase_sn', [
                            'class' => 'form-control',
                            'style' => 'width:150px;',
                    ]),
                    'format' => 'raw',
                    'headerOptions' => ['width'=>'80'],
            ],
            [
                    'attribute' => 'style_sn',
                    'value' => "style_sn",
                    'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                'class' => 'form-control',
                                'style' => 'width:110px;',
                    ]),
                    'format' => 'raw',

            ],
            [
                'attribute' => 'qiban_sn',
                'filter' => true,
                'format' => 'raw',

            ],

            [
                'attribute' => 'goods_num',
                'filter' => false
            ],
            [
                'label' => '出厂数量',
                'value' => function($model){
                    return Yii::$app->supplyService->produce->getShippentNum($model->id);
                }
            ],
            [
                'attribute' => 'bc_status',
                'value' => function ($model){
                    return \addons\Supply\common\enums\BuChanEnum::getValue($model->bc_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'bc_status',\addons\Supply\common\enums\BuChanEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:100px;',
                ]),
                'format' => 'raw',

            ],
//            [
//                'attribute' => 'qiban_type',
//                'value' => function ($model){
//                    return \addons\Style\common\enums\QibanTypeEnum::getValue($model->qiban_type);
//                },
//                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\QibanTypeEnum::getMap(), [
//                    'prompt' => '全部',
//                    'class' => 'form-control',
//                    'style' => 'width:100px;',
//                ]),
//                'format' => 'raw',
//                'headerOptions' => ['width'=>'100'],
//            ],
            [
                'attribute' => 'jintuo_type',
                'value' => function ($model){
                    return \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'qiban_type',\addons\Style\common\enums\JintuoTypeEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:100px;',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'type.name',
                'value' => function($model){
                   return $model->type->name ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'product_type_id',Yii::$app->styleService->productType->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:100px;',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'cate.name',
                'value' => function($model){
                    return $model->cate->name ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'style_cate_id',Yii::$app->styleService->styleCate->getDropDown(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:100px;',
                ]),
                'format' => 'raw',
                'headerOptions' => ['width'=>'100'],
            ],
            [
                'attribute' => 'inlay_type',
                'value' => function($model){                    
                    return Yii::$app->attr->valueName($model->inlay_type);                    
                },
                'filter' => Html::activeDropDownList($searchModel, 'inlay_type',Yii::$app->attr->valueMap(AttrIdEnum::INLAY_METHOD), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:130px;',
                ])

            ],
            [
                    'attribute' => 'peishi_type',
                    'value' => function($model){
                        return PeishiTypeEnum::getValue($model->peishi_type);
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'peishi_type',PeishiTypeEnum::getMap(), [
                            'prompt' => '全部',
                            'class' => 'form-control',
                            'style' => 'width:130px;',
                    ])
            ],
            [
                'attribute' => 'peishi_status',
                'value' => function($model){
                    return \addons\Supply\common\enums\PeishiStatusEnum::getValue($model->peishi_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'peishi_status',\addons\Supply\common\enums\PeishiStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:130px;',
                ])
            ],
            [
                'attribute' => 'peiliao_type',
                'value' => function($model){
                    return PeiliaoTypeEnum::getValue($model->peiliao_type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'peiliao_type',PeiliaoTypeEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control',
                        'style' => 'width:130px;',
                ])                
            ],
            [
                'attribute' => 'peiliao_status',
                'value' => function($model){
                    return \addons\Supply\common\enums\PeiliaoStatusEnum::getValue($model->peiliao_status);
                },
                'filter' => Html::activeDropDownList($searchModel, 'peiliao_status',\addons\Supply\common\enums\PeiliaoStatusEnum::getMap(), [
                    'prompt' => '全部',
                    'class' => 'form-control',
                    'style' => 'width:130px;',
                ])
            ],
            [
                'attribute' => 'factory_distribute_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'factory_distribute_time',
                    'value' => $searchModel->factory_distribute_time,
                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
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
                'value' => function($model){
                    return \Yii::$app->formatter->asDatetime($model->factory_distribute_time);
                }
            ],
            [
                'attribute' => 'factory_order_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'factory_order_time',
                    'value' => $searchModel->factory_order_time,
                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
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
                'value' => function($model){
                    return \Yii::$app->formatter->asDatetime($model->factory_order_time);
                }
            ],
            [
                'attribute' => 'factory_delivery_time',
                'filter' => \kartik\daterange\DateRangePicker::widget([    // 日期组件
                    'model' => $searchModel,
                    'attribute' => 'factory_delivery_time',
                    'value' => $searchModel->factory_delivery_time,
                    'options' => ['readonly' => false,'class'=>'form-control','style'=>'background-color:#fff;width:200px;'],
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
                'value' => function($model){
                    return \Yii::$app->formatter->asDatetime($model->factory_delivery_time);
                }
            ],

            [
                'attribute' => 'follower_name',
                'value' => 'follower_name',
                'filter' => Html::activeTextInput($searchModel, 'follower_name', [
                    'class' => 'form-control',
                    'style' => 'width:80px;',
                ]),                
                'format' => 'raw',
                'headerOptions' => ['width'=>'80'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => ' {action} {status}',
                'buttons' => [
                    'action' => function($url, $model, $key){
                        $buttonHtml = '';
                        switch ($model->bc_status){
                            //确认分配
                            case BuChanEnum::TO_CONFIRMED:
                                $buttonHtml .= Html::edit(['to-confirmed','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '确认分配', [
                                    'class'=>'btn btn-info btn-sm',
                                    'onclick' => 'rfTwiceAffirm(this,"确认分配","确定操作吗？");return false;',

                                ]);
                            //初始化
                            case BuChanEnum::INITIALIZATION:
                                $buttonHtml .= Html::edit(['to-factory','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '分配工厂', [
                                    'class'=>'btn btn-primary btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModal',
                                ]);
                                break;
                            //设置配料信息
                            case BuChanEnum::ASSIGNED:
                                if($model->from_type == \addons\Supply\common\enums\FromTypeEnum::ORDER){
                                    $buttonHtml .= Html::edit(['set-peiliao','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '设置配料信息', [
                                        'class'=>'btn btn-success btn-sm',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#ajaxModalLg',
                                    ]);

                                }
                                break;
                            //待配料
                            case BuChanEnum::TO_PEILIAO :
                                $buttonHtml .= Html::edit(['apply-peiliao','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '申请配料', [
                                    'class'=>'btn btn-success btn-sm',
                                    'style'=>"margin-left:5px",
                                    'onclick' => 'rfTwiceAffirm(this,"开始配料","确定操作吗？");return false;',
                                ]);
                                break;
                            //配料中
                            case BuChanEnum::IN_PEILIAO:

                                break;
                            //已分配
                            case BuChanEnum::TO_PRODUCTION:
                                $buttonHtml .= Html::edit(['to-produce','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '开始生产', [
                                    'class'=>'btn btn-danger btn-sm',
                                    'onclick' => 'rfTwiceAffirm(this,"开始生产","确定操作吗？");return false;',

                                ]);
                                break;
                            //生产中
                            case BuChanEnum::IN_PRODUCTION :
                                ;
                            //部分出厂
                            case BuChanEnum::PARTIALLY_SHIPPED:
                                $buttonHtml .= Html::edit(['produce-shipment','id'=>$model->id ,'returnUrl'=>Url::getReturnUrl()], '生产出厂', [
                                    'class'=>'btn btn-success btn-sm',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#ajaxModalLg',
                                ]);
                                break;

                            default:
                                $buttonHtml .= '';
                        }
                        return $buttonHtml;
                    },                    
                ]
            ]
        ]
      ]);
    ?>
            </div>
        </div>
    </div>
</div>
