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
                            <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'borrow_amount')->textInput(['id'=>'borrow_amount'])?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'borrow_amount')->textInput(['disabled'=>true,'id'=>'borrow_amount_capital'])->label('支付金额（大写）')?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'loan_user_id')->widget(kartik\select2\Select2::class, [
                                'data' => Yii::$app->services->backendMember->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'repay_time')->widget(\kartik\datetime\DateTimePicker::class, [
                                'options' => [
                                    'value' => $model->isNewRecord ? date('Y-m-d') : date('Y-m-d', $model->repay_time),
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ]
                            ]);?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'oa_no')->textInput()?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                           <?= $form->field($model, 'borrow_remark')->textArea(['options'=>['maxlength' => true]])?>
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
    set_borrow_amount_capital();
    $("#borrow_amount").blur(function () {
        set_borrow_amount_capital();
    })
    function set_borrow_amount_capital() {
        var borrow_amount = $("#borrow_amount").val();
        $("#borrow_amount_capital").val(smalltoBIG(borrow_amount))
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