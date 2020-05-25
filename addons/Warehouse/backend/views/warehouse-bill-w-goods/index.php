<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\enums\AuditStatusEnum;

$this->title = '盘点单明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">盘点单详情 - <?php echo $bill->bill_no?></h2>
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
                    <?php if($bill->audit_status == AuditStatusEnum::PENDING) {?>
                        <?= Html::create(['warehouse-bill-w/pandian', 'id' => $bill->id], '盘点', []); ?>
                    <?php }?>    
                    </div>
               </div>
            <div class="box-body table-responsive">  
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showFooter' => true,//显示footer行
                        'id'=>'grid', 
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
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
                                    'headerOptions' => ['width'=>'200'],
                            ],                             
                            [
                                    'label' => '归属仓库',
                                    'attribute' => 'to_warehouse_id',
                                    'value' =>"toWarehouse.name",
                                    'filter'=> \kartik\select2\Select2::widget([
                                            'name'=>'SearchModel[to_warehouse_id]',
                                            'value'=>$searchModel->to_warehouse_id,
                                            'data'=>Yii::$app->warehouseService->warehouse->getDropDown(),
                                            'options' => ['placeholder' =>"请选择"],
                                            'pluginOptions' => [
                                                  'allowClear' => true,
                                            ],
                                    ]),
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'200'],
                            ], 
                            [
                                    'label' => '盘点状态',
                                    'attribute' => 'pandian_status',
                                    'value' =>function($model){
                                        return \addons\Warehouse\common\enums\PandianStatusEnum::getValue($model->pandian_status);
                                    },
                                    'filter'=> \addons\Warehouse\common\enums\PandianStatusEnum::getMap(),
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{view} {edit} {apply-edit} {delete}',
                                'buttons' => [                                                                   
                                    'delete' => function($url, $model, $key) use($bill){
                                        if($bill->audit_status == AuditStatusEnum::PENDING) {
                                            return Html::delete(['delete','id' => $model->id,'bill_id'=>$bill->id,'returnUrl' => Url::getReturnUrl()],'删除',['class' => 'btn btn-danger btn-xs']);
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