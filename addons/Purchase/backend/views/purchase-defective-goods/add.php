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
                 <?= $form->field($model, 'defective_id')->hiddenInput()->label(false) ?>
                 <div class="row">
                     <div class="col-lg-6">
                        <?= $form->field($model, 'receipt_goods_id')->textInput(["placeholder"=>"批量输入请使用逗号或空格或换行符隔开"]) ?>
                     </div>
                     <div class="col-lg-1">
                        <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchDefectiveGoods()"]) ?>
                     </div>
                 </div>
                <div class="box-body table-responsive">
                    <div class="tab-content">
                        <?php
                        $defectiveColomns = [
                            [
                                'name' => 'id',
                                'title'=>"ID",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:60px'
                                ]
                            ],
                            [
                                'name' =>'defective_id',
                                'title'=>"返厂单ID",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:60px'
                                ]
                            ],
                            [
                                'name' =>'receipt_goods_id',
                                'title'=>"收货单商品序号",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'style'=>'width:100px'
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
                                    'readonly' =>'true',
                                    'style'=>'width:100px'
                                ]
                            ],
                            [
                                'name' => "oqc_reason",
                                'title'=>"质检未过原因",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:160px'
                                ],
                                'items' => Yii::$app->purchaseService->fqc->getDropDown()
                            ],
                            [
                                'name' => "oqc_remark",
                                'title'=>"商品备注",
                                'enableError'=>false,
                                'options' => [
                                    'class' => 'input-priority',
                                    'style'=>'width:120px'
                                ]
                            ],
                            [
                                'name' => "style_cate_id",
                                'title'=>"款式分类",
                                'enableError'=>false,
                                'type'  => 'dropDownList',
                                'options' => [
                                    'class' => 'input-priority',
                                    'disabled' => 'true',
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
                                    'disabled' => 'true',
                                    'style'=>'width:100px'
                                ],
                                'items' => Yii::$app->styleService->productType->getDropDown()
                            ],
                            [
                                'name' => "cost_price",
                                'title'=>"成本价",
                                'enableError'=>false,
                                'defaultValue' => '0.00',
                                'options' => [
                                    'class' => 'input-priority',
                                    'readonly' =>'true',
                                    'type' => 'number',
                                    'style'=>'width:80px'
                                ]
                            ]
                        ];
                        ?>
                        <?= unclead\multipleinput\MultipleInput::widget([
                            'name' => "defective_goods_list",
                            'value' => $defectiveGoods,
                            'columns' => $defectiveColomns,
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
function searchDefectiveGoods() {
   var receipt_goods_ids = $.trim($("#purchasedefectivegoods-receipt_goods_id").val());
   if(!receipt_goods_ids) {
	    rfMsg("请输入采购收货单商品序号");
        return false;
   }
    var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['receipt_goods_id','search',])?>&search=1&receipt_goods_id="+receipt_goods_ids;
    window.location.href = url;
}
</script>
