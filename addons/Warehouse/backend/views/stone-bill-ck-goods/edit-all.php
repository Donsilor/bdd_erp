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

$this->title = Yii::t('bill_b_goods', '(石料)其它出库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status)?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::edit(['edit-all', 'bill_id' => $bill->id,'scan'=>1], '添加/编辑金料', ['class'=>'btn btn-success btn-xs']);
            echo '&nbsp;';
            echo Html::a('返回列表', ['stone-bill-ck-goods/index', 'bill_id' => $bill->id], ['class' => 'btn btn-info btn-xs']);
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
                                    <div class="col-sm-9">
                                        <?= Html::textInput('scan_stone_sn', '', ['id'=>'scan_stone_sn','on','class' => 'form-control','placeholder'=>'请输入货号或扫码（多个货号用：“, ”“空格“)']).'<br/>' ?>
                                    </div>
                                    <div class="col-sm-2 text-left">
                                        <button id="scan_submit" type="button" class="btn btn-primary" >保存</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $('#scan_stone_sn').focus();
                            $('#scan_stone_sn').keydown(function(e){
                                if(e.keyCode == 13){
                                    scanGoods();
                                }
                            });
                            $("#scan_submit").click(function(){
                                scanGoods();
                            });
                            function scanGoods(){
                                var stone_sn = $("#scan_stone_sn").val();
                                $.ajax({
                                    type: "post",
                                    url: '<?php echo Url::to(['ajax-scan'])?>',
                                    dataType: "json",
                                    data: {
                                        bill_id: '<?php echo $bill->id?>',
                                        stone_sn:stone_sn,
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
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
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
                                'attribute' => 'stone_sn',
                                'filter' => true,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'stone_type',
                                'value' => function($model){
                                    return Yii::$app->attr->valueName($model->stone_type) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'stone_type',Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::MAT_GOLD_TYPE), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                    'style'=> 'width:80px'
                                ]),
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'style_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'stone_name',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],


                            [
                                'attribute' => 'stone_weight',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'text-align:center;background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => false,
                                'value' =>function($model){
                                    return Html::ajaxInput('stone_weight', $model->stone_weight);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'label'=>'库存总粒数',
                                'attribute' => 'warehouseStone.stock_cnt',
                                'filter' => false,
                                'headerOptions' => ['class' => 'col-md-2','style'=>'text-align:center;'],
                                'contentOptions'=>['style'=>'text-align:center;']
                            ],
                            [
                                'attribute' => 'stone_num',
                                'headerOptions' => ['class' => 'col-md-1', 'style' => 'text-align:center;background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-1', 'style' => 'background-color:#84bf96;'],
                                'filter' => false,
                                'value' =>function($model){
                                    return Html::ajaxInput('stone_num', $model->stone_num);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'stone_price',
                                'filter' => false,
                                'headerOptions' => ['width' => '100','style'=>'text-align:center;'],
                                'contentOptions'=>['style'=>'text-align:center;']
                            ],
                            [
                                'attribute' => 'cost_price',
                                'filter' => false,
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                'attribute' => 'remark',
                                'headerOptions' => ['class' => 'col-md-2', 'style' => 'text-align:center;background-color:#84bf96;'],
                                'footerOptions' => ['class' => 'col-md-2', 'style' => 'background-color:#84bf96;'],
                                'filter' => false,
                                'value' =>function($model){
                                    return Html::ajaxInput('remark', $model->remark);
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
