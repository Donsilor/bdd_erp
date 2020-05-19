<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\AttrTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                
                <?php if($model->style_cate_id && $model->product_type_id) {?>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                        <?php if($model->isNewRecord) {?>
                            <div class="col-lg-4">
                                <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                            </div>
                        <?php }else{ ?>
                            <div class="col-lg-4">
                                <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()",'disabled'=>true]) ?>
                            </div>
                        <?php } ?>
                        
                     </div>
                     <div class="row">                        
                        <div class="col-lg-4">
                            <?= $form->field($model, 'qiban_sn')->textInput(['disabled'=>true, "placeholder"=>"系统自动生成"]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'qiban_type')->dropDownList(\addons\Style\common\enums\QibanTypeEnum::getMap(),['disabled'=>true]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'is_inlay')->dropDownList(\addons\Style\common\enums\InlayEnum::getMap(),['prompt'=>'请选择','disabled'=>true]) ?>
                            <?= $form->field($model, 'is_inlay')->hiddenInput()->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'qiban_name')->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['prompt'=>'请选择']) ?>
                        </div>

                        <div class="col-lg-4">
                            <?= $form->field($model, 'cost_price')->textInput() ?>
                        </div>
					</div>
                  <?php } else {?>
                  <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getGrpDropDown(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                        </div>
                   </div>   
                  <?php }?>
                <?php
                $attr_type = \addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type,'getAttrTypeMap');
                $attr_list = \Yii::$app->styleService->attribute->getAttrListByCateId($model->style_cate_id,$attr_type,$model->is_inlay);
                foreach ($attr_list as $k=>$attr){
                    $attr_field = $attr['is_require'] == 1?'attr_require':'attr_custom';
                    $attr_field_name = "{$attr_field}[{$attr['id']}]";
                    //通用属性值列表
                    $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr['id']);
                    switch ($attr['input_type']){
                        case common\enums\InputTypeEnum::INPUT_TEXT :{
                            $input = $form->field($model,$attr_field_name)->textInput()->label($attr['attr_name']);
                            break;
                        }
                        default:{
                            $input = $form->field($model,$attr_field_name)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr['attr_name']);
                            break;
                        }
                    }//end switch

                    $collLg = 4;
                    ?>
                    <?php if ($k % 3 ==0){ ?><div class="row"><?php }?>
                    <div class="col-lg-<?=$collLg?>"><?= $input ?></div>
                    <?php if(($k+1) % 3 == 0 || ($k+1) == count($attr_list)){?></div><?php }?>
                    <?php
                }//end foreach $attr_list
                ?>
                <?php if($model->style_cate_id) {?>
                        <div class="row">
                            <div class="col-lg-8">
                                <?= $form->field($model, 'remark')->textarea() ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <?= $form->field($model, 'style_image')->widget(common\widgets\webuploader\Files::class, [
                                    'config' => [
                                        'pick' => [
                                            'multiple' => false,
                                            'height'=> '50'
                                        ],
                                        'options'=>[
                                            'height'=>'50'
                                        ]

                                    ],

                                ]); ?>
                            </div>
                        </div>

                <?php }?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function searchGoods() {
        var style_cate_id = $("#qibanattrform-style_cate_id").val();
        var product_type_id = $("#qibanattrform-product_type_id").val();
        var jintuo_type = $("#qibanattrform-jintuo_type").val();
 
        if(!style_cate_id || !product_type_id || !jintuo_type) {
            return false;
        }     
        
        var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['style_cate_id','jintuo_type','product_type_id'])?>?style_cate_id="+style_cate_id+"&product_type_id="+product_type_id+"&jintuo_type="+jintuo_type;
        window.location.href = url;
    }
</script>
