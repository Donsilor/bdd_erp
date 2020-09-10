<?php

use yii\widgets\ActiveForm;
use common\helpers\Url;
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">基本信息</h4>
                </div>

                <div class="box-body">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => $model->formName(),
                            'enableAjaxValidation' => true,
                            'validationUrl' => Url::to(['edit', 'id' => $model['id']]),
                            'fieldConfig' => [
                                //'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                            ]
                        ]);
                    ?>
                   <div class="row">
                       <div class="col-lg-4">
                           <?= $form->field($model, 'finance_no')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"])?>
                       </div>
                       <div class="col-lg-4">
                           <?= $form->field($model, 'apply_user')->textInput(['disabled'=>true])?>
                       </div>
                       <div class="col-lg-4">
                           <?= $form->field($model, 'dept_id')->dropDownList(Yii::$app->services->department::getDropDown(),['disabled'=>true]) ?>
                       </div>
                   </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'contract_name')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'contract_no')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'contract_type')->dropDownList(\addons\Finance\common\enums\ContractTypeEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <?= $form->field($model, 'contract_info')->textArea(['options'=>['maxlength' => true]])?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'amount_total')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'payment_type')->dropDownList(\addons\Finance\common\enums\PaymentTypeEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'last_period_total')->textInput()?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'this_period_amount')->textInput(['id'=>'this_period_amount'])?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'this_period_total')->textInput()?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'this_period_amount')->textInput(['disabled'=>true,'id'=>'this_period_amount_capital_1'])->label('支付金额（大写）')?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'this_period_amount')->textInput(['disabled'=>true,'id'=>'this_period_amount_capital_2'])->label('支付金额（大写）')?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'oa_no')->textInput()?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                           <?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <?php $model->annex_file = !empty($model->annex_file)?explode(',', $model->annex_file):null;?>
                            <?= $form->field($model, 'annex_file')->widget(common\widgets\webuploader\Files::class, [
                                'type' => 'files',
                                'config' => [
                                    'pick' => [
                                        'multiple' => true,
                                    ],

                                ]
                            ]); ?>
                        </div>
                    </div>

                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
<script>
    set_this_period_amount_capital();
    $("#this_period_amount").blur(function () {
        set_this_period_amount_capital();
    })
    function set_this_period_amount_capital() {
        var this_period_amount = $("#this_period_amount").val();
        $("#this_period_amount_capital_1").val(smalltoBIG(this_period_amount))
        $("#this_period_amount_capital_2").val(smalltoBIG(this_period_amount))
    }

    function smalltoBIG(n)
    {
        var fraction = ['角', '分'];
        var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        var unit = [ ['圆', '万', '亿'], ['', '拾', '佰', '仟']  ];
        var head = n < 0? '欠': '';
        n = Math.abs(n);
        var s = '';
        for (var i = 0; i < fraction.length; i++)
        {
            s += (digit[Math.floor(n * 10 * Math.pow(10, i)) % 10] + fraction[i]).replace(/零./, '');
        }
        s = s || '';
        n = Math.floor(n);

        for (var i = 0; i < unit[0].length && n > 0; i++)
        {
            var p = '';
            for (var j = 0; j < unit[1].length && n > 0; j++)
            {
                p = digit[n % 10] + unit[1][j] + p;
                n = Math.floor(n / 10);
            }
            s = p.replace(/(零.)*零$/, '').replace(/^$/, '零')  + unit[0][i] + s;
        }
        return head + s.replace(/(零.)*零圆/, '圆').replace(/(零.)+/g, '零').replace(/^$/, '零圆');
    }


    //文本域自动换行
    $('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
</script>