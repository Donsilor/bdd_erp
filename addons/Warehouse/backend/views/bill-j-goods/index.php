<?php

use common\helpers\Url;
use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\LendStatusEnum;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('bill_j_goods', '借货单明细');
$this->params['breadcrumbs'][] = $this->title;

$lend_status = $bill->billJ->lend_status ?? 0;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no ?> - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
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
            echo Html::edit(['edit-all', 'bill_id' => $bill->id, 'returnUrl' => Yii::$app->request->get('returnUrl')], '编辑货品', ['class' => 'btn btn-info btn-xs']);
            echo '&nbsp;';
        } elseif ($bill->bill_status == BillStatusEnum::CONFIRM
            && in_array($lend_status, [LendStatusEnum::HAS_LEND, LendStatusEnum::PORTION_RETURN])) {
            echo Html::batchPopButton(['batch-return', 'bill_id' => $bill->id, 'check' => 1], '分批还货', [
                'class' => 'btn btn-info btn-xs',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
        }
        //        echo Html::a('导出', ['bill-j/export?ids=' . $bill->id], [
        //            'class' => 'btn btn-success btn-xs'
        //        ]);
        ?>
    </div>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-body table-responsive">
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
                                'attribute' => 'goods_num',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'goodsJ.restore_num',
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
