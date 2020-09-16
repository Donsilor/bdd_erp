<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;

$this->title = '创建外部平台订单';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-9">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px;">                    
                    <div class="row">
                        <div class="col-sm-6"><?= $form->field($model, 'out_trade_no')->textInput()?></div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'sale_channel_id')->widget(\kartik\select2\Select2::class, [
                            'data' => Yii::$app->salesService->saleChannel->getDropDownForExternalOrder(),
                            'options' => ['placeholder' => '请选择',],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);?>              
                        </div>                        
                    </div>                     
                    <div class="row">                    	
                        <div class="col-sm-6"><?= $form->field($model, 'customer_mobile')->textInput()?></div>
                        <div class="col-sm-6">
                        	<?= $form->field($model, 'consignee_id')->widget(\kartik\select2\Select2::class, [
                        	    'data' => $model->getConsigneeMap(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true,                        
                                ],
                            ]);?> 
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-sm-6">
                        	<?= $form->field($model, 'pay_type')->widget(\kartik\select2\Select2::class, [
                                'data' => Yii::$app->salesService->payment->getDropDown(),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true,                        
                                ],
                            ]);?> 
                        </div>                   	
                        <div class="col-sm-6">
                          <?= $form->field($model, 'pay_time')->widget(\kartik\date\DatePicker::class, [
                                'language' => 'zh-CN',
                                'options' => [
                                        'value' =>  Yii::$app->formatter->asDate($model->pay_time ? $model->pay_time : time()),
                                ],
                                'pluginOptions' => [
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,//今日高亮
                                    'autoclose' => true,//选择后自动关闭
                                    'todayBtn' => true,//今日按钮显示
                                ]
                            ]);?>
                        </div>                        
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'language')->dropDownList(common\enums\LanguageEnum::getMap(),['prompt'=>'请选择']);?>              
                        </div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'currency')->dropDownList(common\enums\CurrencyEnum::getMap(),['prompt'=>'请选择']);?>             
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><?= $form->field($model, 'pay_remark')->textArea(['options'=>['maxlength' => true]])?></div>
                        <div class="col-sm-6"><?= $form->field($model, 'remark')->textArea(['options'=>['maxlength' => true]])?></div>            
                    </div>  
                    <?php if($model->isNewRecord) {?>
                    <div class="row">
                        <?= \unclead\multipleinput\MultipleInput::widget([
                                    'max' => 5,
                                    'name' => "ExternalOrderForm[goods_list]",
                                    'value' => $model->goods_list,
                                    'columns' => [                                        
                                        [
                                                'name' => "style_sn",
                                                'title' => '款号',
                                                'enableError' => false,
                                                'options' => [
                                                    'class' => 'input-priority',
                                                    //'style' => 'width:200px',
                                                    'placeholder' => '请输入款号',
                                                ]
                                        ],
                                        [
                                                'name' => 'goods_name',
                                                'title' => '商品名称',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        'style'=>'width:500px',
                                                        'placeholder' => '请输入商品名称',
                                                ]
                                        ],
                                        [
                                                'name' => "goods_pay_price",
                                                'title' => '商品价格',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        //'style' => 'width:200px',
                                                        'placeholder' => '请输入商品价格',
                                                ]
                                        ],
                                        [
                                                'name' => "goods_spec",
                                                'title' => '手寸/尺寸',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        //'style' => 'width:200px',
                                                        'placeholder' => '请输入手寸/尺寸',
                                                ]
                                        ]
                                            
                                    ]
                                ]);?>          
                    </div> 
                    <?php }?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var formId = 'externalorderform';
    $("#"+formId+'-sale_channel_id').change(function(){
        var sale_channel_id = $(this).val();
    	//13台湾momo 7东森
        if(sale_channel_id == 7 || sale_channel_id == 13) {
            $("#"+formId+'-language').val('zh-TW');
            $("#"+formId+'-currency').val('TWD');
        }else {
        	$("#"+formId+'-language').val('zh-TW');
            $("#"+formId+'-currency').val('HKD');
        }   
    })
</script>