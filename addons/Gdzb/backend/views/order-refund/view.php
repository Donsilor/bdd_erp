<?php

use common\enums\GenderEnum;
use common\helpers\Html;
use common\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\order\order */
/* @var $form yii\widgets\ActiveForm */

$this->title = '退货单详情';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>

<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title;?></h2>
    <?php echo Html::menuTab($tabList,$tab)?>

    <div class="row">
         <div class="col-xs-12">
             <div class="box">
                 <div class="col-xs-6">
                     <div class="box">
                         <div class="box-body table-responsive">
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('refund_sn') ?>：</td>
                                     <td><?= $model->refund_sn ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('channel_id') ?>：</td>
                                     <td><?= $model->saleChannel->name ?? '' ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('warehouse_id') ?>：</td>
                                     <td><?= $model->warehouse->name ?? '' ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"> 客户微信：</td>
                                     <td><?= $model->customer->realname ?? ''; ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('refund_status') ?>：</td>
                                     <td><?= \addons\Sales\common\enums\RefundStatusEnum::getValue($model->refund_status) ?></td>
                                 </tr>

                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('created_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDate($model->created_at) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-1 text-right"><?= $model->getAttributeLabel('updated_at') ?>：</td>
                                     <td><?= \Yii::$app->formatter->asDate($model->updated_at) ?></td>
                                 </tr>
                             </table>
                         </div>
                     </div>
                 </div>
                 <div class="col-xs-6" style="padding: 0px;">
                     <div class="box" style="margin-bottom: 0px;">
                         <div class="box-body table-responsive" >
                             <table class="table table-hover">
                                 <tr>
                                     <td class="col-xs-3 text-right">订单号：</td>
                                     <td><?= $model->order->order_sn ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('refund_amount') ?>：</td>
                                     <td><?= $model->refund_amount ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('refund_num') ?>：</td>
                                     <td><?= $model->refund_num ?></td>
                                 </tr>


                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('audit_status') ?>：</td>
                                     <td><?= \common\enums\AuditStatusEnum::getValue($model->audit_status) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('audit_time') ?>：</td>
                                     <td><?= Yii::$app->formatter->asDate($model->audit_time) ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('auditor_id') ?>：</td>
                                     <td><?= $model->auditor->username ?? '' ?></td>
                                 </tr>
                                 <tr>
                                     <td class="col-xs-3 text-right"><?= $model->getAttributeLabel('audit_remark') ?>：</td>
                                     <td><?= $model->remark ?></td>
                                 </tr>

                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

        <div class="box-footer text-center">
            <?php
            if($model->audit_status == \common\enums\AuditStatusEnum::SAVE){
                echo Html::edit(['ajax-apply','id'=>$model->id], '提审', [
                    'class'=>'btn btn-success btn-ms',
                    'onclick' => 'rfTwiceAffirm(this,"提交审核", "确定提交吗？");return false;',
                ]);
            }
            ?>
            <?php
            if($model->audit_status == \common\enums\AuditStatusEnum::PENDING ){
                echo Html::edit(['ajax-audit','id'=>$model->id], '审核', [
                    'class'=>'btn btn-success btn-ms',
                    'data-toggle' => 'modal',
                    'data-target' => '#ajaxModal',
                ]);
            }
            ?>

        </div>
        <div class="col-xs-12">
            <div class="box" style="margin:0px">
                <div class="box-header" style="margin:0">
                </div>
                <div class="table-responsive col-lg-12">
                    <?php $order = $model ?>
                    <?= \yii\grid\GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'options' => ['id' => 'order-goods', 'style' => ' width:100%;white-space:nowrap;'],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible' => false,
                            ],
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'id',  //设置每行数据的复选框属性
                                'headerOptions' => ['width' => '30'],
                            ],
                            'id',
                            [
                                'attribute' => 'goods_sn',
                                'value' => 'goods_sn',
                                'headerOptions' => ['class' => 'col-md-1'],
                            ],
                            [
                                'attribute' => 'goods_name',
                                'value' => function ($model) {
                                    return "<div style='width:200px;white-space:pre-wrap;'>" . $model->goods_name . "</div>";
                                },
                                'format' => 'raw',
                            ],

                            [
                                'attribute' => 'style_cate_id',
                                'value' => function ($model) {
                                    return $model->styleCate->name ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'product_type_id',
                                'value' => function ($model) {
                                    return $model->productType->name ?? '';
                                },
                                'format' => 'raw',
                            ],

                            [
                                'attribute' => 'warehouse_id',
                                'value' => function ($model) {
                                    return $model->warehouse->name ?? '';
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'cost_price',
                                'value' => function ($model) {
                                    return $model->cost_price;
                                }
                            ],
                            [
                                'attribute' => 'goods_price',
                                'value' => function ($model) {
                                    return $model->goods_price;
                                }
                            ],
                            [
                                'attribute' => 'refund_price',
                                'value' => function ($model) {
                                    return $model->refund_price;
                                }
                            ],
                            [
                                'attribute' => 'is_factory',
                                'value' => function ($model) {
                                    return \common\enums\ConfirmEnum::getValue($model->is_factory);
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => '操作',
                                //'headerOptions' => ['width' => '150'],
                                'template' => '{factory} ',
                                'buttons' => [
                                    'factory' => function ($url, $model, $key) use ($order) {
                                        if ($order->audit_status == \common\enums\AuditStatusEnum::PASS) {
                                            return Html::edit(['ajax-factory', 'id' => $model->id], '是否返厂', [
                                                'class' => 'btn btn-primary btn-xs',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#ajaxModal',
                                            ]);

                                        }
                                    },
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>

    </div>
</div>


