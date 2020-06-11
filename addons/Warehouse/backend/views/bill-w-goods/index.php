<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Warehouse\common\enums\BillStatusEnum;

$this->title = '盘点单明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">盘点单详情 - <?php echo $bill->bill_no?> - <?php echo BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="tab-content">
        <div class="row col-xs-15">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                    <?= Html::encode($this->title) ?>
                    <?php //echo Html::checkboxList('colmun','',\Yii::$app->purchaseService->purchaseGoods->listColmuns(1))?>
                    </h3>
                    <div class="box-tools">
                    <?php if($bill->bill_status == BillStatusEnum::SAVE) {?>
                        <?= Html::create(['bill-w/pandian', 'id' => $bill->id,'returnUrl'=>Url::getReturnUrl()], '盘点', []); ?>
                    <?php }?>    
                    </div>
               </div>
            <div class="box-body table-responsive">  
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => false,//显示footer行
                        'id'=>'grid', 
                        'columns' => [
                            [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => false,
                            ],
                            [
                                    'attribute' => 'id',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'80'],
                            ],
                            [
                                    'attribute' => 'goods_id',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],
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
                                    'attribute' => 'style_sn',
                                    'filter' => true,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'120'],
                            ],
                            [
                                    'label' => '盘点仓库',
                                    'attribute' => 'to_warehouse_id',
                                    'value' =>"toWarehouse.name",
                                    'filter'=> false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],
                            ],                             
                            [
                                    'label' => '归属仓库',
                                    'attribute' => 'from_warehouse_id',
                                    'value' =>"fromWarehouse.name",
                                    'filter'=> \kartik\select2\Select2::widget([
                                            'name'=>'SearchModel[from_warehouse_id]',
                                            'value'=>$searchModel->from_warehouse_id,
                                            'data'=>Yii::$app->warehouseService->warehouse->getDropDown(),
                                            'options' => ['placeholder' =>"请选择"],
                                            'pluginOptions' => [
                                                  'allowClear' => true,
                                            ],
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'180'],
                            ], 
                            [
                                    'label' => '盘点状态',
                                    'attribute' => 'status',
                                    'value' =>function($model){
                                        return \addons\Warehouse\common\enums\PandianStatusEnum::getValue($model->status);
                                    },
                                    'filter'=> Html::activeDropDownList($searchModel, 'status',\addons\Warehouse\common\enums\PandianStatusEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',                                            
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'110'],
                            ],
                            [
                                    'label' => '调整状态',
                                    'attribute' => 'goodsW.adjust_status',
                                    'value' =>function($model){
                                        return \addons\Warehouse\common\enums\PandianAdjustEnum::getValue($model->goodsW->adjust_status ?? '');
                                    },
                                    'filter'=> Html::activeDropDownList($searchModel, 'goodsW.adjust_status',\addons\Warehouse\common\enums\PandianAdjustEnum::getMap(), [
                                            'prompt' => '全部',
                                            'class' => 'form-control',
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'110'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{edit}',
                                'buttons' => [                                                                   
                                    'edit' => function($url, $model, $key) use($bill){
                                        
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