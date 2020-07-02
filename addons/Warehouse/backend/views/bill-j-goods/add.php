<?php

use unclead\multipleinput\MultipleInput;
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\StyleSexEnum;

$this->title = '新增货品';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                 <div class="row">
                     <div class="col-lg-3">
                        <?= $form->field($model, 'goods_ids')->textInput(["placeholder"=>"批量输入请使用逗号或空格或换行符隔开"]) ?>
                     </div>
                     <div class="col-lg-1">
                        <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchWarehouseGoods()"]) ?>
                     </div>
                 </div>
                <div class="box-body table-responsive">
                    <div class="tab-content">
                        <?php
                        $warehouseColomns = [
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
                                'name' =>'style_sn',
                                'title'=>"款号",
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
                                    'readonly' =>'true',
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
                                'name' => "put_in_type",
                                'title'=>"入库方式",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'disabled' =>'true',
                                    'style'=>'width:120px',
                                    'prompt'=>'请选择',
                                ],
                                'items' => \addons\Warehouse\common\enums\PutInTypeEnum::getMap()
                            ],
                            [
                                'name' => "warehouse_id",
                                'title'=>"仓库",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'disabled' =>'true',
                                    'style'=>'width:120px',
                                    'prompt'=>'请选择',
                                ],
                                'items' => Yii::$app->warehouseService->warehouse::getDropDown()
                            ],
                            [
                                'name' => "material",
                                'title'=>"主成色",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'disabled' =>'true',
                                    'style'=>'width:120px',
                                    'prompt'=>'请选择',
                                ],
                                'defaultValue' => 0,
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MATERIAL)
                            ],
                            [
                                'name' => "gold_weight",
                                'title'=>"金重",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'readonly' =>'true',
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
                                    'readonly' =>'true',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "diamond_carat",
                                'title'=>"钻石大小",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "diamond_color",
                                'title'=>"钻石颜色",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'disabled' =>'true',
                                    'style'=>'width:100px',
                                    'prompt'=>'请选择',
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE_COLOR)
                            ],
                            [
                                'name' => "diamond_clarity",
                                'title'=>"钻石净度",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'disabled' =>'true',
                                    'style'=>'width:100px',
                                    'prompt'=>'请选择',
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE_CLARITY)
                            ],
                            [
                                'name' => "diamond_cert_id",
                                'title'=>"证书号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
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
                                    'readonly' =>'true',
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
                                    'readonly' =>'true',
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
                                    'readonly' =>'true',
                                    'style'=>'width:80px'
                                ]
                            ]
                        ];
                        ?>
                        <?= unclead\multipleinput\MultipleInput::widget([
                            'name' => "bill_goods",
                            'addButtonOptions'=>['label'=>'','class'=>''],
                            'value' => $warehouse_goods,
                            'columns' => $warehouseColomns,
                        ]) ?>
                    </div>
                </div>
               <!-- ./box-body -->
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function searchWarehouseGoods() {
        var goods_ids = $.trim($("#warehousebillcform-goods_ids").val());
        if(!goods_ids) {
            rfMsg("请输入货号");
            return false;
        }
        var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['goods_ids','search',])?>&search=1&goods_ids="+goods_ids;
        window.location.href = url;
    }
</script>
