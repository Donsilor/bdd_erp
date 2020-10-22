<?php

use yii\grid\GridView;
use common\helpers\Html;
use addons\Warehouse\common\enums\BillStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('bill_c_goods', '其它出库单明细');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $bill->bill_no ?>
        - <?= \addons\Warehouse\common\enums\BillStatusEnum::getValue($bill->bill_status) ?></h2>
    <?php echo Html::menuTab($tabList, $tab) ?>
    <div style="float:right;margin-top:-40px;margin-right: 20px;">
        <?php
        if ($bill->bill_status == BillStatusEnum::SAVE) {
            echo Html::create(['add', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get("returnUrl")], '批量添加货品', [
                'class' => 'btn btn-primary btn-xs openIframe',
                'data-width' => '90%',
                'data-height' => '90%',
                'data-offset' => '20px',
            ]);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id, 'scan' => 1,'returnUrl'=>Yii::$app->request->get("returnUrl")], '扫码添加货品', ['class' => 'btn btn-primary btn-xs']);
            echo '&nbsp;';
            echo Html::edit(['edit-all', 'bill_id' => $bill->id,'returnUrl'=>Yii::$app->request->get("returnUrl")], '编辑货品', ['class' => 'btn btn-info btn-xs']);
            echo '&nbsp;';
        }
        echo Html::a('单据打印', ['bill-c/print', 'id' => $bill->id], [
            'target' => '_blank',
            'class' => 'btn btn-info btn-xs',
        ]);
        echo '&nbsp;';
        echo Html::a('明细导出', ['bill-c/export?ids=' . $bill->id], [
            'class' => 'btn btn-warning btn-xs',
            'onclick' => 'rfTwiceAffirm(this,"明细导出", "确定导出吗？");return false;',
        ]);
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
                        //'options' => ['style'=>' width:120%;white-space:nowrap;'],
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
                                'label' => '出库数量',
                                'attribute' => 'goods_num',
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
                                'visible' => \common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CHUKU_PRICE),
                                'filter' => false,
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
