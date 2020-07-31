<?php

use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\ConfirmEnum;
use common\helpers\Html;
use common\helpers\Url;
use kartik\select2\Select2;
use yii\grid\GridView;
use addons\Supply\common\enums\BuChanEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;

$this->title = '赠品采购详情';
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
                echo Html::batchPop(['warehouse', 'check'=>1],'分批收货', [
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
                        'options' => ['style'=>'white-space:nowrap;'],
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
                            /*[
                                    'attribute' => 'id',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'100'],
                            ],*/
                            [
                                'label' => '商品图片',
                                'value' => function ($model) {
                                    return \common\helpers\ImageHelper::fancyBox(Yii::$app->purchaseService->gift->getStyleImage($model),90,90);
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
                                    $str = $model->goods_name;
                                    return $str;
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
                                    $str = $model->goods_sn;
                                    return $str;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'100'],
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
                                'attribute' => 'style_sex',
                                'format' => 'raw',
                                'value' => function ($model){
                                    return \addons\Style\common\enums\StyleSexEnum::getValue($model->style_sex);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'style_sex',\addons\Style\common\enums\StyleSexEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'material_color',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material_color);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'material_color',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MATERIAL_COLOR), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:100px;'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'finger_hk',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->finger_hk);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger_hk',Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute' => 'finger',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->finger);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'finger',Yii::$app->attr->valueMap(AttrIdEnum::FINGER), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'chain_length',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->chain_length ?? '';
                                },
                                'filter' => Html::activeTextInput($searchModel, 'chain_length', [
                                    'class' => 'form-control',
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'main_stone_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->main_stone_type);
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'main_stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAIN_STONE_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px;'
                                ]),
                                'headerOptions' => [],
                            ],
                            [
                                'attribute'=>'main_stone_num',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->main_stone_num ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_size',
                                'filter' => Html::activeTextInput($searchModel, 'goods_size', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    $str = $model->goods_size;
                                    return $str;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute'=>'goods_num',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->goods_num ?? '';
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                    'attribute' => 'goods_weight',
                                    'value' => function ($model) {
                                        return $model->goods_weight ;
                                    },
                                    'filter' => false,                                    
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute'=>'gold_price',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return $model->gold_price ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            [
                                    'attribute'=>'cost_price',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return $model->cost_price ;
                                    },
                                    'headerOptions' => ['width'=>'150'],
                            ],
                            /*[
                                'attribute'=>'incl_tax_price',
                                'filter' => Html::activeTextInput($searchModel, 'incl_tax_price', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    return $model->incl_tax_price??"0.00";
                                },
                                'headerOptions' => ['width'=>'150'],
                            ],*/
                            [
                                    'attribute' => 'is_receipt',
                                    'value' => function ($model){
                                        return ConfirmEnum::getValue($model->is_receipt);
                                    },
                                    'filter' => Html::activeDropDownList($searchModel, 'is_receipt',ConfirmEnum::getMap(), [
                                        'prompt' => '全部',
                                        'class' => 'form-control',
                                        'style' => 'width:80px;',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                'attribute'=>'remark',
                                'filter' => Html::activeTextInput($searchModel, 'remark', [
                                    'class' => 'form-control',
                                ]),
                                'value' => function ($model) {
                                    $str = $model->remark;
                                    return $str;
                                },
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'300'],
                            ],
                            /*[
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
                            ],*/                            
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{edit} {delete}',
                                'buttons' => [ 
                                    'edit' => function($url, $model, $key) use($purchase){
                                         if($purchase->purchase_status == PurchaseStatusEnum::SAVE) {
                                             return Html::edit(['edit','id' => $model->id],'编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
                                         }                                         
                                    },
                                    'apply-edit' =>function($url, $model, $key){                                            
                                         return Html::edit(['apply-edit','id' => $model->id],'申请编辑',['class' => 'btn btn-primary btn-xs openIframe','data-width'=>'90%','data-height'=>'90%','data-offset'=>'20px']);
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