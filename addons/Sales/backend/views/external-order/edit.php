<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Sales\common\enums\DeliveryTypeEnum;

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
                        <div class="col-sm-6">
                        	<?= $form->field($model, 'platform_id')->widget(\kartik\select2\Select2::class, [
                        	    'data' => Yii::$app->salesService->platform->getDropDown(DeliveryTypeEnum::Platform),
                                'options' => ['placeholder' => '请选择'],
                                'pluginOptions' => [
                                    'allowClear' => true,                        
                                ],
                            ]);?> 
                        </div>
                        <div class="col-sm-6"><?= $form->field($model, 'out_trade_no')->textInput()?></div>
                                               
                    </div>                     
                    <div class="row">                    	
                        <div class="col-sm-6"><?= $form->field($model, 'customer_mobile')->textInput()?></div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'sale_channel_id')->widget(\kartik\select2\Select2::class, [
                            'data' => Yii::$app->salesService->saleChannel->getDropDown(),
                            'options' => ['placeholder' => '请选择',],
                            'pluginOptions' => [
                                'allowClear' => true
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
                        <div class="col-sm-6"><?= $form->field($model, 'other_fee')->textInput()?></div>
                        <div class="col-sm-6"><?= $form->field($model, 'arrive_amount')->textInput()?></div>
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
                                                'title' => '*款号',
                                                'enableError' => false,
                                                'options' => [
                                                    'class' => 'input-priority',
                                                    'placeholder' => '请输入款号',
                                                ]
                                        ],
                                        [
                                                'name' => 'goods_name',
                                                'title' => '*商品名称',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        'style'=>'width:300px',
                                                        'placeholder' => '请输入商品名称',
                                                ]
                                        ],
                                        [
                                                'name' => "goods_price",
                                                'title' => '*商品价格',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        'placeholder' => '请输入商品价格',
                                                ]
                                        ],
                                        [
                                                'name' => "size",
                                                'title' => '尺寸(cm)',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                         'placeholder' => '请输入尺寸(cm)',
                                                ]
                                        ],
                                        [
                                                'name' => "finger_type",
                                                'title'=>"手寸类型",
                                                'enableError'=>false,
                                                'type'  => 'dropDownList',
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        'style' =>'width:80px'
                                                ],
                                                'defaultValue' => '',
                                                'items' => [''=>'请选择','HK'=>'港号','US'=>'美号']
                                        ],
                                        [
                                                'name' => "finger",
                                                'title' => '手寸',
                                                'enableError' => false,
                                                'options' => [
                                                        'class' => 'input-priority',
                                                        'placeholder' => '请输入手寸',
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
    $("#"+formId+'-platform_id').change(function(){
        var platform_id = $(this).val();
        var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['platform_id'])?>?platform_id="+platform_id;
        window.location.href = url;   
    })
</script>