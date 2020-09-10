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
                 <?= $form->field($model, 'receipt_id')->hiddenInput()->label(false) ?>
                 <div class="row">
                     <div class="col-lg-3">
                        <?= $form->field($model, 'produce_sn')->textInput(["placeholder"=>"批量输入请使用逗号或空格或换行符隔开"]) ?>
                     </div>
                     <div class="col-lg-1">
                        <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchReceiptGoods()"]) ?>
                     </div>
                 </div>
                <div class="box-body table-responsive">
                    <div class="tab-content">
                        <?php
                        $receiptColomns = [
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
                                'name' =>'purchase_sn',
                                'title'=>"采购单编号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:160px'
                                ]
                            ],
                            [
                                'name' =>'produce_sn',
                                'title'=>"布产单编号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:160px'
                                ]
                            ],
                            [
                                'name' =>'barcode',
                                'title'=>"条形码编号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:120px'
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
                                'name' =>'style_sn',
                                'title'=>"款号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:120px'
                                ]
                            ],
                            [
                                'name' => "factory_mo",
                                'title'=>"工厂模号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "style_cate_id",
                                'title'=>"款式分类",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ],
                                'items' => Yii::$app->styleService->styleCate->getDropDown()
                            ],
                            [
                                'name' => "product_type_id",
                                'title'=>"产品线",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ],
                                'items' => Yii::$app->styleService->productType->getDropDown()
                            ],
                            [
                                'name' => "finger",
                                'title'=>"指圈",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "xiangkou",
                                'title'=>"镶口",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "material",
                                'title'=>"主成色",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ],
                                'defaultValue' => 0,
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MATERIAL)
                            ],
                            [
                                'name' => "gold_weight",
                                'title'=>"主成色重",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "gold_price",
                                'title'=>"主成色买入单价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "gold_loss",
                                'title'=>"金损",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "jintuo_type",
                                'title'=>"金托类型",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ],
                                'items' => \addons\Style\common\enums\JintuoTypeEnum::getMap()
                            ],
                            [
                                'name' => "gross_weight",
                                'title'=>"毛重",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "suttle_weight",
                                'title'=>"净重",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "cost_price",
                                'title'=>"成本价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
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
                                'defaultValue' => '0.00',
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
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "cert_id",
                                'title'=>"证书号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "main_stone",
                                'title'=>"主石",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE)
                            ],
                            [
                                'name' => "main_stone_num",
                                'title'=>"主石数量",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "main_stone_weight",
                                'title'=>"主石重",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "main_stone_color",
                                'title'=>"主石颜色",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE_COLOR)
                            ],
                            [
                                'name' => "main_stone_clarity",
                                'title'=>"主石净度",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::MAIN_STONE_CLARITY)
                            ],
                            [
                                'name' => "main_stone_price",
                                'title'=>"主石买入单价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "second_cert_id1",
                                'title'=>"副石证书号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "second_stone1",
                                'title'=>"副石1",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::SECOND_STONE)
                            ],
                            [
                                'name' => "second_stone_num1",
                                'title'=>"副石1数量",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "second_stone_weight1",
                                'title'=>"副石1重量",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "second_stone_price1",
                                'title'=>"副石1买入单价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "second_stone2",
                                'title'=>"副石2",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::SECOND_STONE)
                            ],
                            [
                                'name' => "second_stone_num2",
                                'title'=>"副石2数量",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "second_stone_weight2",
                                'title'=>"副石2重量",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "second_stone_price2",
                                'title'=>"副石2买入单价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "second_stone3",
                                'title'=>"副石3",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::SECOND_STONE)
                            ],
                            [
                                'name' => "second_stone_num3",
                                'title'=>"副石3数量",
                                'enableError'=>false,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "second_stone_weight3",
                                'title'=>"副石3重量",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "second_stone_price3",
                                'title'=>"副石3买入单价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "markup_rate",
                                'title'=>"加价率",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "gong_fee",
                                'title'=>"工费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "parts_weight",
                                'title'=>"配件重量",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "parts_price",
                                'title'=>"配件金额",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "parts_fee",
                                'title'=>"配件工费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "xianqian_fee",
                                'title'=>"镶嵌工费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "biaomiangongyi",
                                'title'=>"表面工艺",
                                'type' => 'dropDownList',
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ],
                                'items' => \Yii::$app->attr->valueMap(\addons\Purchase\common\enums\ReceiptGoodsAttrEnum::BIAOMIANGONGYI)
                            ],
                            [
                                'name' => "biaomiangongyi_fee",
                                'title'=>"表面工艺工费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "fense_fee",
                                'title'=>"分色工艺工费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "bukou_fee",
                                'title'=>"补口工费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "cert_fee",
                                'title'=>"证书费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "extra_stone_fee",
                                'title'=>"超石费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "tax_fee",
                                'title'=>"税费",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "other_fee",
                                'title'=>"其它费用",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ],
                            [
                                'name' => "goods_remark",
                                'title'=>"商品备注",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:80px'
                                ]
                            ]
                        ];
                        ?>
                        <?= unclead\multipleinput\MultipleInput::widget([
                            'name' => "receipt_goods_list",
                            'value' => $receipt_goods,
                            'columns' => $receiptColomns,
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
function searchReceiptGoods() {
   var produce_sns = $.trim($("#purchasereceiptgoods-produce_sn").val());
   if(!produce_sns) {
	    rfMsg("请输入布产单编号");
        return false;
   }
    var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['produce_sns','search',])?>&search=1&produce_sns="+produce_sns;
    window.location.href = url;
}
</script>
