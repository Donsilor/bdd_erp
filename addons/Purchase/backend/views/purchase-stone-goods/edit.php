<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin(['id' => 'purchasestonegoodsform']); ?>
            <div class="box-body" style="padding:20px 50px">
              <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false) ?>
              <div class="row">
                  <div class="col-lg-4">
                      <?= $form->field($model, 'goods_sn')->widget(\kartik\select2\Select2::class, [
                          'data' => Yii::$app->styleService->stone->getDropDown(),
                          'options' => ['placeholder' => '请选择'],
                          'pluginOptions' => [
                              'allowClear' => false
                          ],
                      ]);?>
                  </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'goods_name')->textInput() ?>
                </div>
                 <div class="col-lg-4">
                    <?= $form->field($model, 'stone_type')->dropDownList($model->getStoneTypeMap(),['prompt'=>'请选择', 'disabled'=>'disabled']) ?>
                </div>
              </div>
              <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_weight')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_price')->textInput() ?>
                    </div>
			   </div>
			   <div class="row">
                   <div class="col-lg-4">
                       <?= $form->field($model, 'cost_price')->textInput(['disabled'=>true]) ?>
                   </div>
                   <div class="col-lg-4">
                       <?= $form->field($model, 'stone_color')->dropDownList($model->getColorMap(),['prompt'=>'请选择']) ?>
                   </div>
                   <div class="col-lg-4">
                       <?= $form->field($model, 'stone_clarity')->dropDownList($model->getClarityMap(),['prompt'=>'请选择']) ?>
                   </div>
			   </div>
                <div class="row">
                    <div class="col-lg-4 div1">
                        <?= $form->field($model, 'stone_cut')->dropDownList($model->getCutMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4 div1">
                        <?= $form->field($model, 'stone_symmetry')->dropDownList($model->getSymmetryMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4 div1">
                        <?= $form->field($model, 'stone_polish')->dropDownList($model->getPolishMap(),['prompt'=>'请选择']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 div1">
                        <?= $form->field($model, 'stone_fluorescence')->dropDownList($model->getFluorescenceMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_shape')->dropDownList($model->getShapeMap(),['prompt'=>'请选择', 'disabled'=>'disabled']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_colour')->dropDownList($model->getColourMap(),['prompt'=>'请选择']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_type')->dropDownList($model->getCertTypeMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_id')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_size')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'channel_id')->widget(\kartik\select2\Select2::class, [
                            'data' => \Yii::$app->salesService->saleChannel->getDropDown(),
                            'options' => ['placeholder' => '请选择'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'spec_remark')->textarea() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'remark')->textarea() ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var formId = 'purchasestonegoodsform';
    function fillStoneForm(){
        var goods_sn = $("#"+formId+"-goods_sn").val();
        if(goods_sn != '') {
            $.ajax({
                type: "get",
                url: '<?php echo Url::to(['ajax-get-stone'])?>',
                dataType: "json",
                data: {
                    'goods_sn': goods_sn,
                },
                success: function (data) {
                    if (parseInt(data.code) == 200 && data.data) {
                        $("#"+formId+"-goods_name").val(data.data.goods_name);
                        $("#"+formId+"-stone_type").val(data.data.stone_type);
                        $("#"+formId+"-stone_shape").val(data.data.stone_shape);
                        load(data.data.stone_type);
                    }
                }
            });
        }
    }
    $("#"+formId+"-goods_sn").change(function(){
        fillStoneForm();
    });

    $(document).ready(function(){
        var id = $("#"+formId+"-stone_type").find(':checked').val();
        load(id);
    });

    function load(id) {
        if($.inArray(id,['241', 241])>=0){
            $(".div1").hide();

            $("#"+formId+"-stone_cut").select2("val",'');
            $("#"+formId+"-stone_symmetry").select2("val",'');
            $("#"+formId+"-stone_polish").select2("val",'');
            $("#"+formId+"-stone_fluorescence").select2("val",'');
        }else {
            $(".div1").show();
        }
    }
</script>