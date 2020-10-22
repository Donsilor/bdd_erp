<?php

use yii\grid\GridView;
use common\helpers\Url;
use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;

$this->title = Yii::t('bill_c_goods', '其它出库单明细');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no ?>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div class="box-tools" style="float:right;margin-top:-40px; margin-right: 20px;">
        <?php
        if ($bill->bill_status == \addons\Warehouse\common\enums\BillStatusEnum::SAVE) {
            echo Html::create(['add', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get("returnUrl")], '批量添加商品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id, 'scan' => 1,'returnUrl'=>Yii::$app->request->get("returnUrl")], '扫码添加商品', ['class' => 'btn btn-primary btn-xs']);
            echo '&nbsp;';
            echo Html::a('返回列表', ['bill-c-goods/index', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get("returnUrl")], ['class' => 'btn btn-info btn-xs']);
        }
        ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
                    <?php if (Yii::$app->request->get('scan')) { ?>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group field-cate-sort">
                                    <div class="col-sm-6">
                                        <?= Html::textInput('scan_goods_id', '', ['id' => 'scan_goods_id', 'on', 'class' => 'form-control', 'placeholder' => '请输入货号 或 扫商品条码录入']) . '<br/>' ?>
                                    </div>
                                    <div class="col-sm-2 text-left">
                                        <button id="scan_submit" type="button" class="btn btn-primary">保存</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $('#scan_goods_id').focus();
                            $('#scan_goods_id').keydown(function (e) {
                                if (e.keyCode == 13) {
                                    scanGoods();
                                }
                            });
                            $("#scan_submit").click(function () {
                                scanGoods();
                            });

                            function scanGoods() {
                                var goods_id = $("#scan_goods_id").val();
                                $.ajax({
                                    type: "post",
                                    url: '<?php echo Url::to(['ajax-scan'])?>',
                                    dataType: "json",
                                    data: {
                                        bill_id: '<?php echo $bill->id?>',
                                        goods_id: goods_id,
                                    },
                                    success: function (data) {
                                        window.location.href = '<?= \Yii::$app->request->getUrl(); ?>';
                                    }
                                });
                            }
                        </script>
                    <?php } ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['style' => 'white-space:nowrap;font-size:12px;'],
                        'showFooter' => false,//显示footer行
                        'id' => 'grid',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                            ],
                            [
                                'attribute' => 'id',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods_id',
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods.style_sn',
                                'filter' => Html::activeTextInput($searchModel, 'style_sn', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute' => 'goods.goods_name',
                                'filter' => Html::activeTextInput($searchModel, 'goods_name', [
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'label' => '最大出库数量',
                                'attribute' => 'goods_num',
                                'format' => 'raw',
                                'value' => function ($model) {                                    
                                    return $model->goods->stock_num + $model->goods_num;
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '出库数量',
                                'attribute' => 'goods_num',
                                'headerOptions' => ['style' => 'background-color:#FFFF88;'],
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->goods->goods_num > 1) {
                                        return Html::ajaxInput('goods_num', $model->goods_num, [/*'onfocus' => 'rfClearVal(this)',*/ 'data-type' => 'number','data-id' => $model->id,'data-url'=>'ajax-chuku-num']);
                                    }
                                    return $model->goods_num ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '仓库',
                                'attribute' => 'warehouse_id',
                                'value' => "warehouse.name",
                                'filter' => false/* Select2::widget([
                                    'name'=>'SearchModel[warehouse_id]',
                                    'value'=>$searchModel->warehouse_id,
                                    'data'=>Yii::$app->warehouseService->warehouse::getDropDown(),
                                    'options' => ['placeholder' =>"请选择"],
                                    'pluginOptions' => [
                                        'allowClear' => true,

                                    ],
                                ]) */,
                            ],
                            [
                                'attribute' => 'material_type',
                                'value' => function ($model) {
                                    return Yii::$app->attr->valueName($model->goods->material_type) ?? "";
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '手寸',
                                'value' => function ($model) {
                                    $finger = '';
                                    if ($model->goods->finger ?? false) {
                                        $finger .= Yii::$app->attr->valueName($model->goods->finger) . '(US)';
                                    }
                                    if ($model->goods->finger_hk ?? false) {
                                        $finger .= ' ' . Yii::$app->attr->valueName($model->goods->finger_hk) . '(HK)';
                                    }
                                    return $finger;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.suttle_weight',
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
                                'attribute' => 'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'chuku_price',
                                'headerOptions' => ['style' => 'background-color:#FFFF88;'],
                                'footerOptions' => ['style' => 'background-color:#FFFF88;'],
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CHUKU_PRICE),
                                'filter' => false,
                                'value' => function ($model) {
                                    return $model->chuku_price ?? 0;
                                    //return Html::ajaxInput('chuku_price', $model->chuku_price, ['style' => "border:1px solid #BBD6FF"]);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'label' => '采购成本总额',
                                'attribute' => 'cost_price',
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE),
                                'value' => function ($model) {
                                    return bcmul($model->cost_price, $model->goods_num, 3) ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods_remark',
                                'filter' => true,
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'delete' => function ($url, $model, $key) use ($bill) {
                                        if ($bill->bill_status == BillStatusEnum::SAVE) {
                                            return Html::delete(['delete', 'id' => $model->id], '删除', ['class' => 'btn btn-danger btn-xs']);
                                        }
                                    },
                                ],
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script  type="text/javascript">
    function rfClearVal(obj) {
        var val = $(obj).val();
        if (val <= 0) {
            $(obj).val("");
        }
    }
</script>

