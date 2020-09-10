<?php


use common\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\DeliveryTypeEnum;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel yii\data\ActiveDataProvider */
/* @var $tabList yii\data\ActiveDataProvider */
/* @var $tab yii\data\ActiveDataProvider */
/* @var $bill yii\data\ActiveDataProvider */

$this->title = Yii::t('bill_b_goods', '其它出库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['add', 'bill_id' => $bill->id], '商品批量添加', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width'=>'90%',
                'data-height'=>'90%',
                'data-offset'=>'20px',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id,'scan'=>1], '商品扫码添加', ['class'=>'btn btn-success btn-xs']);
            echo '&nbsp;';
            echo Html::a('返回列表', ['bill-c-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-info btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                   <?php if(Yii::$app->request->get('scan')) {?>
                   <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group field-cate-sort">
                                <div class="col-sm-6">
                                    <?= Html::textInput('scan_goods_id', '', ['id'=>'scan_goods_id','on','class' => 'form-control','placeholder'=>'请输入货号 或 扫商品条码录入']).'<br/>' ?>
                                </div>
                                <div class="col-sm-2 text-left">
                                    <button id="scan_submit" type="button" class="btn btn-primary" >保存</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                    $('#scan_goods_id').focus();
                    $('#scan_goods_id').keydown(function(e){
                        if(e.keyCode == 13){
                        	scanGoods();
                        }
                    });
                     $("#scan_submit").click(function(){
                    	 scanGoods();
                     });
                     function scanGoods(){
                    	 var goods_id = $("#scan_goods_id").val();
                         $.ajax({
                             type: "post",
                             url: '<?php echo Url::to(['ajax-scan'])?>',
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
                   <?php }?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        //'options' => ['style'=>' width:120%;white-space:nowrap;'],
                        'options' => ['style'=>'white-space:nowrap;'],
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
                            ],
                            [
                                'attribute' => 'id',
                                'filter' => false,
                                'format' => 'raw',
                            ],
                            [
                                'attribute'=>'goods_id',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods_name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],
                            [
                                'attribute'=>'goods.goods_status',
                                'value' => function($model){
                                     return \addons\Warehouse\common\enums\GoodsStatusEnum::getValue($model->goods->goods_status);
                                },
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods.style_cate_id',
                                'value' => 'goods.styleCate.name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods.product_type_id',
                                'value' => 'goods.productType.name',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],

                            [
                                'attribute' => 'warehouse_id',
                                'value' =>"warehouse.name",
                                'filter'=>false,
                                'headerOptions' => ['class' => 'col-md-2'],
                            ],

                            [
                                'attribute' => 'material',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->material);
                                },
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'gold_weight',
                                'filter' => false,
                            ],

                            [
                                'attribute' => 'goods.main_stone_type',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.diamond_carat',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.main_stone_num',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.second_stone_weight1',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.second_stone_num1',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.finger',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.cert_id',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'chuku_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CHUKU_PRICE),
                                'filter' => false,
                                'value' =>function($model){
                                    return Html::ajaxInput('chuku_price', $model->chuku_price);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function($url, $model, $key) use($bill){
                                        if($bill->bill_status == BillStatusEnum::SAVE){
                                            return Html::delete(['delete', 'id' => $model->id],'删除',['class'=>'btn btn-danger btn-xs']);
                                        }
                                    },
                                ],
                                'headerOptions' => ['class' => 'col-md-3'],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
