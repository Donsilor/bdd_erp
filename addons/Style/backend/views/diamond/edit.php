<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\langbox\LangBox;
use yii\base\Widget;
use common\widgets\skutable\SkuTable;
use common\helpers\Url;
use common\enums\AreaEnum;
use common\helpers\AmountHelper;

/* @var $this yii\web\View */
/* @var $model addons\Style\common\models\Style */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('goods', '裸钻添加/编辑');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
?>
<?php $form = ActiveForm::begin([
         'id' => $model->formName(),
        'enableAjaxValidation' => false,
        'validationUrl' => Url::to(['edit', 'id' => $model['id']]),
        'fieldConfig' => [
            'template' => "{label}{input}{hint}",

        ],
]); ?>
<div class="box-body nav-tabs-custom">
     <h2 class="page-header"><?php echo Yii::t('goods', '裸钻添加/编辑');?></h2>
     <div class="tab-content">     
       <div class="row nav-tabs-custom tab-pane tab0 active">
            <h4 class="box-title" style="font-weight: bold;"> 基础信息</h4>
            <div class="box-body col-lg-9" style="margin-left:9px">
                <div class="row">

                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_type')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CERT_TYPE),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cert_id')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'sale_price')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">


                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_num')->textInput() ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'goods_name')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'remark')->textarea(['maxlength' => true]) ?>
                    </div>

                </div>



                <div class="row">
<!--                    <div class="col-lg-3">--><?//= $form->field($model, 'status')->radioList(\common\enums\StatusEnum::getMap()) ?><!--</div>-->
                    <div class="col-lg-3"><?= $form->field($model, 'is_stock')->radioList(\addons\Sales\common\enums\IsStockEnum::getMap()) ?></div>
                </div>




    		    <!-- ./nav-tabs-custom -->
            </div>
        <!-- ./box-body -->
      </div>
      <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_2">
          <h4 class="box-title" style="font-weight: bold;"> 属性信息</h4>
          <div class="box-body" style="margin-left:10px">
              <div class="row">
                  <div class="col-lg-4">
                      <?= $form->field($model, 'carat')->textInput()->hint('ct',['tag'=>'span','class'=>'unit']) ?>
                  </div>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'shape')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SHAPE),['prompt'=>'请选择']) ?>
                  </div>

                  <div class="col-lg-4">
                      <?= $form->field($model, 'color')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_COLOR),['prompt'=>'请选择']) ?>
                  </div>
                  
              </div>
              <div class="row">
                  <div class="col-lg-4">
                      <?= $form->field($model, 'clarity')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CLARITY),['prompt'=>'请选择']) ?>
                  </div>

                  <div class="col-lg-4">
                      <?= $form->field($model, 'cut')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_CUT),['prompt'=>'请选择']) ?>
                  </div>

                  <div class="col-lg-4">
                      <?= $form->field($model, 'polish')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_POLISH),['prompt'=>'请选择']) ?>
                  </div>


              </div>
              <div class="row">
                  <div class="col-lg-4">
                      <?= $form->field($model, 'symmetry')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_SYMMETRY),['prompt'=>'请选择']) ?>
                  </div>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'fluorescence')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_FLUORESCENCE),['prompt'=>'请选择']) ?>
                  </div> 
                  <div class="col-lg-4">
                      <?= $form->field($model, 'stone_floor')->dropDownList(Yii::$app->attr->valueMap(\addons\Style\common\enums\AttrIdEnum::DIA_STONE_FLOOR),['prompt'=>'请选择']) ?>
                  </div>                 

              </div>

              <div class="row">
              	  <div class="col-lg-4">
                      <?= $form->field($model, 'depth_lv')->textInput()->hint('%',['tag'=>'span','class'=>'unit']) ?>
                  </div>
			      <div class="col-lg-4">
                      <?= $form->field($model, 'table_lv')->textInput()->hint('%',['tag'=>'span','class'=>'unit']) ?>
                  </div>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'aspect_ratio')->textInput()->hint('%',['tag'=>'span','class'=>'unit']) ?>
                  </div>
              </div>
              <div class="row">
                  <div class="col-lg-4">
                      <?= $form->field($model, 'length')->textInput(['maxlength' => true])->hint('mm',['tag'=>'span','class'=>'unit']) ?>
                  </div>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'width')->textInput(['maxlength' => true])->hint('mm',['tag'=>'span','class'=>'unit']) ?>
                  </div>

              </div>



          </div>
      	 <!-- ./box-body -->          
      </div>    
    
      <div class="row nav-tabs-custom tab-pane tab0 active" id="tab_3">
        <h4 class="box-title" style="font-weight: bold;"> 其他信息</h4>
        <div class="box-body col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                <?= $form->field($model, 'goods_image')->widget(common\widgets\webuploader\Files::class, [
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],

                    ]
                ]); ?>
                </div>
                <div class="col-lg-6">
                <?= $form->field($model, 'goods_gia_image')->widget(common\widgets\webuploader\Files::class, [
                    'type' => 'files',
                    'config' => [
                        'pick' => [
                            'multiple' => false,
                        ],
                        'formData' => [
//                        'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                        ],
                    ]
                ]); ?>
                </div>
            </div>


              <?php $model->parame_images = !empty($model->parame_images)?explode(',', $model->parame_images):null;?>
              <?= $form->field($model, 'parame_images')->widget(common\widgets\webuploader\Files::class, [
                  'config' => [
                      'pick' => [
                          'multiple' => true,
                      ],
                      'formData' => [
                          //'drive' => 'local',// 默认本地 支持 qiniu/oss 上传
                      ],
                  ]
              ]); ?>



          </div>
          <!-- ./box-body -->          
      </div>


      <!-- ./row -->
    </div>
    <div class="modal-footer">
        <div class="col-sm-12 text-center">
            <button class="btn btn-primary" type="submit">保存</button>
            <span class="btn btn-white" onclick="history.go(-1)">返回</span>
        </div>
	</div>
</div>

<?php ActiveForm::end(); ?>

<script>
    //裸钻编号根据证书号获取
    $('input[name="Diamond[cert_id]"]').on('change',function (){
        $('input[name="Diamond[goods_sn]"]').val('DSN' + $(this).val());
    });


    // 商品名称根据石重、形状、颜色、净度、证书类型 设置
    $('input[name="Diamond[carat]"]').on('change',function (){
        setGoodsName();
    });

    $('select[name="Diamond[cert_type]"]').on('change',function (){
        setGoodsName();
    });
    $('select[name="Diamond[shape]"]').on('change',function (){
        setGoodsName();
    });
    $('select[name="Diamond[color]"]').on('change',function (){
        setGoodsName();
    });
    $('select[name="Diamond[clarity]"]').on('change',function (){
        setGoodsName();
    });

    function setGoodsName(){
        var carat = $('input[name="Diamond[carat]"]').val();
        var cert_type = $('select[name="Diamond[cert_type]"]').children('option:selected').val();
        var shape = $('select[name="Diamond[shape]"]').children('option:selected').val();
        var color = $('select[name="Diamond[color]"]').children('option:selected').val();
        var clarity = $('select[name="Diamond[clarity]"]').children('option:selected').val();
        var param_data = {carat:carat,cert_type:cert_type,shape:shape,color:color,clarity:clarity}

        $.ajax({
            type: "post",
            url: 'get-goods-name',
            dataType: "json",
            data: param_data,
            success: function (data) {
                if (parseInt(data.code) !== 200) {
                    // rfMsg(data.message);
                } else {
                    console.log(data.data);
                    $('input[name="Diamond[goods_name]"]').val(data.data);

                }
            }
        });
    }



</script>

