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
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
              <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false) ?>
              <div class="row">
                      <div class="col-lg-4">
                          <?= $form->field($model, 'material_type')->dropDownList($model->getMaterialTypeMap(),['prompt'=>'请选择']) ?>
                      </div>
                      <div class="col-lg-4">
                          <?= $form->field($model, 'goods_sn')->widget(\kartik\select2\Select2::class, [
                              'data' => Yii::$app->styleService->gold->getDropDown(),
                              'options' => ['placeholder' => '请选择'],
                              'pluginOptions' => [
                                  'allowClear' => false
                              ],
                          ]);?>
                      </div>
                      <div class="col-lg-4">
                          <?= $form->field($model, 'goods_name')->textInput() ?>
                      </div>

              </div>
			   <div class="row">
                   <div class="col-lg-4">
                       <?= $form->field($model, 'goods_weight')->textInput() ?>
                   </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'gold_price')->textInput() ?>
                    </div>
                   <div class="col-lg-4">
                       <?= $form->field($model, 'cost_price')->textInput(['disabled'=>'disabled']) ?>
                   </div>
               </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'incl_tax_price')->textInput() ?>
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
    function getGoodsSn() {
        var material_type = $("#"+formId+"-material_type").val();
        var html = '<option>请选择</option>';
        $.ajax({
            url: '<?php echo Url::to(['get-goods-sn'])?>',
            type: 'post',
            dataType: 'json',
            data: {material_type: material_type},
            success: function (msg) {
                console.log(msg.data)
                $.each(msg.data, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#"+formId+"-goods_sn").html(html);
                $("#"+formId+"-goods_name").val('');
            }
        })
    }

    $("#"+formId+"-goods_sn").change(function(){
        fillStoneForm();
    });

    $("#"+formId+"-material_type").change(function(){
        getGoodsSn();
    });
</script>