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
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
          <?php if($bill->bill_status < BillStatusEnum::CONFIRM) {?>
                <?= Html::create(['bill-w-goods/edit-all', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get("returnUrl")], '盘点', [
                        'class'=>'btn btn-success btn-xs',
                        
                ]); ?>
                <?= Html::create(['bill-w-goods/ajax-import', 'bill_id' => $bill->id], '批量导入', [
                        'class'=>'btn btn-success btn-xs',
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',                        
                ]); ?>                
           		<?= Html::create(['bill-w/ajax-adjust', 'id' => $bill->id], '刷新盘点', [
           		        'onclick' => 'rfTwiceAffirm(this,"刷新盘点","确定刷新盘点单吗？");return false;']
           		);?>
                <?= Html::create(['bill-w/ajax-finish','id'=>$bill->id], '盘点结束', [
                        'class'=>'btn btn-warning btn-xs',
                        'onclick' => 'rfTwiceAffirm(this,"盘点结束","确定结束盘点单吗？");return false;',
                ]);?>
           <?php }?>      
   
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
                                    'headerOptions' => ['width'=>'250'],
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
                                    'filter'=> false,
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'150'],
                            ], 
                            [
                                    'attribute'=>'goods.goods_status',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods->goods_status);
                                    },
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                    'attribute'=>'goodsW.should_num',
                                    'filter' => false,
                                    'value' => function ($model) {
                                            return $model->goodsW->should_num;
                                    },
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'100'],
                            ],
                            [
                                    'attribute'=>'goodsW.actual_num',
                                    'filter' => false,
                                    'value' => function ($model) {
                                        return $model->goodsW->actual_num;
                                    },
                                    'format' => 'raw',
                                    'headerOptions' => ['width'=>'100'],
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