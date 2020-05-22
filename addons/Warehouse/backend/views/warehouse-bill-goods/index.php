<?php


use common\helpers\Html;
use common\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = '单据明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['action' => Url::to(['ajax-edit'])]); ?>
<div class="box-body nav-tabs-custom">
    <h2 class="page-header"><?php echo $this->title; ?> - <?php echo $billInfo->bill_no ?></h2>
    <?php echo Html::menuTab($tabList, $tab)?>
    <div class="tab-content">
        <div class="row col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                    <?php //echo Html::checkboxList('colmun','',\Yii::$app->purchaseService->purchaseGoods->listColmuns(1))?>
                    </h3>
                    <div class="box-tools">
                        <?= Html::create(['edit', 'receipt_id' => $billInfo->id], '新增货品', [
                            'class' => 'btn btn-primary btn-xs openIframe',
                            'data-width'=>'90%',
                            'data-height'=>'90%',                            
                            'data-offset'=>'20px',
                        ]); ?>
                    </div>
               </div>
            <div class="box-body table-responsive">
                <div class="tab-content">
                    <?php
                    $billgoodsColomns = [
                        [
                            'name' => 'id',
                            'title'=>"序号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:60px'
                            ]
                        ],
                        [
                            'name' =>'goods_id',
                            'title'=>"货号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:160px'
                            ]
                        ],
                        [
                            'name' =>'bill_no',
                            'title'=>"单据编号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:160px'
                            ]
                        ],
                        [
                            'name' =>'goods_name',
                            'title'=>"商品名称",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:120px'
                            ]
                        ],
                        [
                            'name' =>'style_sn',
                            'title'=>"款号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:120px'
                            ]
                        ],
                        [
                            'name' =>'goods_num',
                            'title'=>"商品数量",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'readonly' =>'true',
                                'style'=>'width:60px'
                            ]
                        ],
                        [
                            'name' => "material",
                            'title'=>"主成色",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:100px'
                            ]
                        ],
                        [
                            'name' => "gold_weight",
                            'title'=>"金重",
                            'enableError'=>false,
                            'defaultValue' => 0,
                            'options' => [
                                'class' => 'input-priority',
                                'type' => 'number',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "gold_loss",
                            'title'=>"金损",
                            'enableError'=>false,
                            'defaultValue' => 0,
                            'options' => [
                                'class' => 'input-priority',
                                'type' => 'number',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "diamond_carat",
                            'title'=>"钻石大小",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "diamond_color",
                            'title'=>"钻石颜色",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "diamond_clarity",
                            'title'=>"钻石净度",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "diamond_cert_id",
                            'title'=>"证书号",
                            'enableError'=>false,
                            'options' => [
                                'class' => 'input-priority',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "cost_price",
                            'title'=>"成本价",
                            'enableError'=>false,
                            'defaultValue' => 0,
                            'options' => [
                                'class' => 'input-priority',
                                'type' => 'number',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "sale_price",
                            'title'=>"销售价",
                            'enableError'=>false,
                            'defaultValue' => 0,
                            'options' => [
                                'class' => 'input-priority',
                                'type' => 'number',
                                'style'=>'width:80px'
                            ]
                        ],
                        [
                            'name' => "market_price",
                            'title'=>"市场价",
                            'enableError'=>false,
                            'defaultValue' => 0,
                            'options' => [
                                'class' => 'input-priority',
                                'type' => 'number',
                                'style'=>'width:80px'
                            ]
                        ]
                    ];
                    ?>
                    <?= unclead\multipleinput\MultipleInput::widget([
                        'name' => "bill_goods_list",
                        'value' => $billGoods,
                        'columns' => $billgoodsColomns,
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 text-center">
                    <?= $form->field($billInfo, 'id')->hiddenInput()->label(false) ?>
                    <button class="btn btn-primary" type="submit">保存</button>
                    <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                </div>
            </div>
        <!-- box end -->
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>