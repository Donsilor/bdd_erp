<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Supply\common\enums\BuChanEnum;
use common\enums\AuditStatusEnum;

$this->title = '物料采购详情';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?> - <?php echo $purchase->purchase_sn?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
            if($purchase->purchase_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE){
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
                                    'attribute' => 'goods_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],
                            ],                            
                            [
                                    'attribute' => 'style_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'120'],
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
                                    'headerOptions' => ['width'=>'100'],
                            ],

                            [
                                    'attribute'=>'goods_name',
                                    'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                            'class' => 'form-control',
                                    ]),
                                    'value' => function ($model) {
                                         $str = $model->goods_name;
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
                                   'headerOptions' => ['width'=>'100'],
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
                                    'headerOptions' => ['width' => '100'],
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
                                    'attribute' => '布产状态',
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
                                        return Html::edit(['view','id' => $model->id,'search'=>1,'returnUrl' => Url::getReturnUrl()],'商品详情',[
                                            'class' => 'btn btn-info btn-xs',
                                        ]);
                                    },
                                    'edit' => function($url, $model, $key) use($purchase){
                                         if($purchase->audit_status == AuditStatusEnum::PENDING) {
                                             return Html::edit(['edit','id' => $model->id],'商品编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }                                         
                                    },
                                    'apply-edit' =>function($url, $model, $key){
                                        if($model->produce_id && $model->produce && $model->produce->bc_status <= BuChanEnum::IN_PRODUCTION) {
                                            return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                        }
                                    },                                    
                                    'delete' => function($url, $model, $key) use($purchase){
                                        if($purchase->audit_status == AuditStatusEnum::PENDING) {
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