<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillWStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;

$this->title = '盘点单明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header">盘点单详情 - <?php echo $bill->bill_no?> - <?php echo BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">          
          <?= Html::create(['bill-w-goods/index', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get("returnUrl")], '返回列表', []); ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-15">
            <div class="box">
               <div class="box-body table-responsive"> 
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group field-cate-sort">
                                <div class="col-sm-6">
                                    <?= Html::textInput('goods_id', '', ['id'=>'goods_id','class' => 'form-control','placeholder'=>'请输入货号 或 扫商品条码盘点']).'<br/>' ?>
                                </div>
                                <div class="col-sm-2 text-left">
                                    <button id="pandianBtn" type="button" class="btn btn-primary" >盘点</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    'filter'=> false,/*\kartik\select2\Select2::widget([
                                            'name'=>'SearchModel[from_warehouse_id]',
                                            'value'=>$searchModel->from_warehouse_id,
                                            'data'=>Yii::$app->warehouseService->warehouse->getDropDown(),
                                            'options' => ['placeholder' =>"请选择"],
                                            'pluginOptions' => [
                                                  'allowClear' => true,
                                            ],
                                    ]),*/
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
                                        if($model->goodsW->should_num > 1 ) {
                                            return Html::ajaxInput('actual_num', $model->goodsW->actual_num, ['data-id' => $model->id,'data-url'=>'ajax-pandian-num']);
                                        }else{
                                            return $model->goodsW->actual_num;
                                        }                                        
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

<script type="text/javascript">
    $('#goods_id').focus();
    $('#goods_id').keydown(function(e){
        if(e.keyCode == 13){
        	scanGoods();
        }
    });
     $("#pandianBtn").click(function(){
    	 scanGoods();
     });
     function scanGoods(){
    	 var goods_id = $("#goods_id").val();
         $.ajax({
             type: "post",
             url: '<?php echo Url::to(['bill-w-goods/ajax-pandian'])?>',
             dataType: "json",
             data: {
                 bill_id: '<?php echo $bill->id?>',
                 goods_id:goods_id,
             },
             success: function (data) {
                 window.location.href='<?= \Yii::$app->request->getUrl(); ?>';
             }
         });
     }                       
</script>