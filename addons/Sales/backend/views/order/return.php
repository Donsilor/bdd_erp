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
                                    'value' => 'goods_name',
                                    'contentOptions' => ['style' => 'width:200px;word-wrap:break-word;'],
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
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'return_type')->radioList(\addons\Sales\common\enums\ReturnTypeEnum::getMap()) ?>
                    </div>
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'return_by')->radioList(\addons\Sales\common\enums\ReturnByEnum::getMap()) ?>
<!--                    </div>-->
                </div>
                <div class="row">
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'is_finance_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
<!--                    </div>-->
                    <div class="col-lg-6">
                        <?= $form->field($model, 'is_quick_refund')->radioList(\common\enums\ConfirmEnum::getMap()) ?>
                    </div>
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
                        <?= $form->field($model, 'return_reason')->textarea() ?>
                    </div>
<!--                    <div class="col-lg-6">-->
<!--                        --><?//= $form->field($model, 'remark')->textarea() ?>
<!--                    </div>-->
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var formId = 'purchasegoldgoodsform';
    function fillStoneForm(){
        var goods_sn = $("#"+formId+"-goods_sn").val();
        if(goods_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-gold'])?>',
                dataType: "json",
                data: {
                    'goods_sn': goods_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#"+formId+"-goods_name").val(data.data.goods_name);
                        $("#"+formId+"-material_type").val(data.data.gold_type);
                    }
                }
            });
        }
    }
    $("#"+formId+"-goods_sn").change(function(){
        fillStoneForm();
    });
</script>