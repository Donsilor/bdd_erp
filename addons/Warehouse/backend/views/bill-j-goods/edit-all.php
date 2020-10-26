<?php

use common\helpers\Url;
use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('bill_j_goods', '借货单编辑');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no ?>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div style="float:right;margin-top:-40px;margin-right: 20px;">
        <?php
        if ($bill->bill_status == BillStatusEnum::SAVE) {
            echo Html::create(['add', 'bill_id' => $bill->id], '新增货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id, 'scan' => 1, 'returnUrl' => Yii::$app->request->get('returnUrl')], '商品扫码添加', ['class' => 'btn btn-success btn-xs']);
            echo '&nbsp;';
        }
        echo Html::a('返回列表', ['bill-j-goods/index', 'bill_id' => $bill->id, 'returnUrl' => Yii::$app->request->get('returnUrl')], ['class' => 'btn btn-white btn-xs']);
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
                                        <button id="scan_submit" type="button" class="btn btn-primary btn-ms">保存
                                        </button>
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
                    <?php echo Html::batchButtons(false) ?>
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
                                'attribute' => 'goods_id',
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'style_sn',
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods_name',
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'goods.stock_num',
                                'value' => function ($model) {
                                    return $model->goods_num + $model->goods->stock_num;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods_num',
                                'headerOptions' => ['style' => 'background-color:#7BBFEA;'],
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->goods->goods_num > 1) {
                                        return Html::ajaxInput('goods_num', $model->goods_num, [/*'onfocus' => 'rfClearVal(this)',*/ 'data-type' => 'number', 'data-id' => $model->id, 'data-url' => 'ajax-lend-num']);
                                    }
                                    return $model->goods_num ?? 0;
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.lend_status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return \addons\Warehouse\common\enums\LendStatusEnum::getValue($model->goodsJ->lend_status) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goodsJ.lend_status', \addons\Warehouse\common\enums\LendStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute' => 'goods.style_cate_id',
                                'value' => 'goods.styleCate.name',
                                'filter' => true,
                            ],
                            [
                                'attribute' => 'warehouse_id',
                                'value' => "warehouse.name",
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.material_type',
                                'value' => function ($model) {
                                    return \Yii::$app->attr->valueName($model->goods->material_type ?? false) ?? '';
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods.material_color',
                                'value' => function ($model) {
                                    return \Yii::$app->attr->valueName($model->goods->material_color ?? false) ?? '';
                                },
                                'filter' => false,
                            ],
                            [
                                'label' => '手寸',
                                'value' => function ($model) {
                                    $finger = '';
                                    if ($model->goods->finger ?? false) {
                                        $finger .= \Yii::$app->attr->valueName($model->goods->finger) . '(US)';
                                    }
                                    if ($model->goods->finger_hk ?? false) {
                                        $finger .= ' ' . \Yii::$app->attr->valueName($model->goods->finger_hk) . '(HK)';
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
                                'attribute' => 'goods.main_stone_type',
                                'value' => function ($model) {
                                    return \Yii::$app->attr->valueName($model->goods->main_stone_type ?? false) ?? '';
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.receive_id',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->goodsJ->receive->username ?? "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.receive_time',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->goodsJ->receive_time) {
                                        return \Yii::$app->formatter->asDatetime($model->goodsJ->receive_time) ?? "";
                                    }
                                    return "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.receive_remark',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->goodsJ->receive_remark ?? "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.qc_status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return \addons\Warehouse\common\enums\QcStatusEnum::getValue($model->goodsJ->qc_status) ?? "";
                                },
                                'filter' => Html::activeDropDownList($searchModel, 'goodsJ.qc_status', \addons\Warehouse\common\enums\QcStatusEnum::getMap(), [
                                    'prompt' => '全部',
                                    'class' => 'form-control',
                                ]),
                            ],
                            [
                                'attribute' => 'goodsJ.qc_remark',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->goodsJ->qc_remark ?? "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.restore_time',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->goodsJ->restore_time) {
                                        return \Yii::$app->formatter->asDatetime($model->goodsJ->restore_time) ?? "";
                                    }
                                    return "";
                                },
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goods_remark',
                                'filter' => false,
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                'template' => '{delete}',
                                'buttons' => [
                                    'edit' => function ($url, $model, $key) use ($bill) {
                                        if ($model->goodsJ->lend_status == \addons\Warehouse\common\enums\LendStatusEnum::IN_RECEIVE) {
                                            return Html::edit(['ajax-edit', 'id' => $model->id, 'returnUrl' => Url::getReturnUrl()], '编辑', [
                                                'class' => 'btn btn-primary btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModal',
                                            ]);
                                        }
                                    },
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