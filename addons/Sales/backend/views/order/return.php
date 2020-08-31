<?php

use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\helpers\Url;

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body">
                <label class="control-label">商品信息</label>
                <div class="box-body table-responsive">
                    <div class="row">
                    <div class="col-lg-12">
                        <?= GridView::widget([
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
                                    'name' => 'ids',  //设置每行数据的复选框属性
                                    'headerOptions' => ['width' => '30'],
                                ],
                                'id',
                                [
                                    'attribute' => 'goods_name',
                                    'value' => function ($model) {
                                        return "<div style='width:200px;white-space:pre-wrap;'>" . $model->goods_name . "</div>";
                                    },
                                    'format' => 'raw',
                                ],
                                [
                                    'attribute' => 'goods_id',
                                    'value' => 'goods_id',
                                    'headerOptions' => ['class' => 'col-md-1'],
                                ],
                                [
                                    'attribute' => 'goods_sn',
                                    'value' => 'goods_sn',
                                    'headerOptions' => ['class' => 'col-md-1'],
                                ],
                                [
                                    'attribute' => 'goods_price',
                                    'value' => function ($model) {
                                        return common\helpers\AmountHelper::outputAmount($model->goods_price, 2, $model->currency);
                                    }
                                ],
                                [
                                    'attribute' => 'goods_discount',
                                    'value' => function ($model) {
                                        return common\helpers\AmountHelper::outputAmount($model->goods_discount, 2, $model->currency);
                                    }
                                ],
                                [
                                    'attribute' => 'goods_pay_price',
                                    'value' => function ($model) {
                                        return common\helpers\AmountHelper::outputAmount($model->goods_pay_price, 2, $model->currency);
                                    }
                                ],
                            ]
                        ]); ?>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'return_type')->radioList(\addons\Sales\common\enums\ReturnTypeEnum::getMap()) ?>
                    </div>
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'return_by')->radioList(\addons\Sales\common\enums\ReturnByEnum::getMap()) ?>
<!--                    </div>-->
                    <div class="col-lg-6">
                        <?= $form->field($model, 'is_quick_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'return_reason')->dropDownList(\Yii::$app->salesService->returnConfig->getDropDown(),['prompt'=>'请选择']);?>
                    </div>
                    <div id="div1" class="col-lg-6" style="display:none;">
                        <?= $form->field($model, 'new_order_sn')->textInput() ?>
                    </div>
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'is_finance_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
<!--                    </div>-->
                </div>
<!--                <div class="row">-->
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'bank_name')->textInput() ?>
<!--                    </div>-->
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'bank_card')->textInput() ?>
<!--                    </div>-->
<!--                </div>-->
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'remark')->textarea() ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var formId = 'returnform';
    //function fillStoneForm(){
    //    var goods_sn = $("#"+formId+"-goods_sn").val();
    //    if(goods_sn != '') {
    //        $.ajax({
    //            type: "get",
    //            url: '<?php //echo Url::to(['ajax-get-gold'])?>//',
    //            dataType: "json",
    //            data: {
    //                'goods_sn': goods_sn,
    //            },
    //            success: function (data) {
    //                if (parseInt(data.code) == 200 && data.data) {
    //                    $("#"+formId+"-goods_name").val(data.data.goods_name);
    //                    $("#"+formId+"-material_type").val(data.data.gold_type);
    //                }
    //            }
    //        });
    //    }
    //}
    //$("#"+formId+"-goods_sn").change(function(){
    //    fillStoneForm();
    //});
    $("#"+formId+"-return_type").change(function(){
        var type = $(this).find(':checked').val();
        if(type == 2){
            $("#div1").show();
        }else {
            //$("#"+formId+"-new_order_sn").find('select').find("option:first").prop("selected",true);
            $("#"+formId+"-new_order_sn").val("");
            $("#div1").hide();
        }
    })
</script>