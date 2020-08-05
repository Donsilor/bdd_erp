<?php
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\QibanTypeEnum;

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                 <?= $form->field($model, 'apply_id')->hiddenInput()->label(false) ?>
			     <?php if($model->style_id) {?>
			         <div class="row">
    			         <?php if($model->isNewRecord) {?>      			    
            			 <div class="col-lg-3">         
                			<?= $form->field($model, 'style_sn')->textInput() ?>
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			     <?php }else{?>
        			     <div class="col-lg-4">
                			<?= $form->field($model, 'style_sn')->textInput(['disabled'=>'disabled']) ?>
            			 </div>
        			     <?php }?>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_name')->textInput() ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'goods_type')->dropDownList(\addons\Style\common\enums\QibanTypeEnum::getMap(),['disabled'=>true]) ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'style_cate_id')->dropDownList(Yii::$app->styleService->styleCate->getDropDown(),['disabled'=>true]) ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'product_type_id')->dropDownList(Yii::$app->styleService->productType->getDropDown(),['disabled'=>true]) ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'style_sex')->dropDownList(\addons\Style\common\enums\StyleSexEnum::getMap(),['disabled'=>true]) ?>
                         </div>
        			 </div>
                     <div class="row">
                         <div class="col-lg-4">
                             <?= $form->field($model, 'jintuo_type')->dropDownList(\addons\Style\common\enums\JintuoTypeEnum::getMap(),['prompt'=>'请选择','onchange'=>"searchGoods()"]) ?>
                         </div>
                         <div class="col-lg-4">
                             <?= $form->field($model, 'cost_price')->textInput() ?>
                         </div>
                     </div>



    			<?php }else{?>
        			<div class="row">
            			 <div class="col-lg-4">         
                			<?= $form->field($model, 'style_sn')->textInput() ?>
            			 </div>
            			 <div class="col-lg-1">
                            <?= Html::button('查询',['class'=>'btn btn-info btn-sm','style'=>'margin-top:27px;','onclick'=>"searchGoods()"]) ?>
        			     </div>
        			</div>
    			<?php }?>        			 

            	<?php
                $attr_list = \Yii::$app->styleService->attribute->module(\addons\Style\common\enums\AttrModuleEnum::PURCHASE)->getAttrListByCateId($model->style_cate_id,\addons\Style\common\enums\JintuoTypeEnum::getValue($model->jintuo_type,'getAttrTypeMap'),$model->is_inlay);
                foreach ($attr_list as $k=>$attr){
                    $attr_id  = $attr['id'];//属性ID
                    $is_require = $attr['is_require'];
                    $attr_name = \Yii::$app->attr->attrName($attr_id);//属性名称

                    $_field = $is_require == 1 ? 'attr_require':'attr_custom';
                    $field = "{$_field}[{$attr_id}]";
                    switch ($attr['input_type']){
                        case common\enums\InputTypeEnum::INPUT_TEXT :{
                            $input = $form->field($model,$field)->textInput()->label($attr_name);
                            break;
                        }
                        default:{
                            $attr_values = Yii::$app->styleService->styleAttribute->getDropdowns($model->style_id,$attr_id);
                            if(empty($attr_values)) {
                                $attr_values = Yii::$app->styleService->attribute->getValuesByAttrId($attr_id);
                            }
                            $input = $form->field($model,$field)->dropDownList($attr_values,['prompt'=>'请选择'])->label($attr_name);
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
               <!-- ./box-body -->
                <?php if($model->style_id) {?>
                <div style="margin: 0px 0 20px 0;">
                    <h3 class="box-title"> 其他信息</h3>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'stone_info')->textarea() ?>
                    </div>                   
                    <div class="col-lg-4">
                        <?= $form->field($model, 'parts_info')->textarea() ?>
                    </div>
                     <div class="col-lg-4">
                		<?= $form->field($model, 'remark')->textarea() ?>
                	</div>
            	</div>
                    <div class="row">
                        <div class="col-lg-6">
                            <?= $form->field($model, 'goods_images')->widget(common\widgets\webuploader\Files::class, [
                                'config' => [
                                    'pick' => [
                                        'multiple' => true,
                                    ],
                                ]
                            ]); ?>
                        </div>
                        <div class="col-lg-6" style="padding: 0px;">
                            <?php $model->goods_video = !empty($model->goods_video)?explode(',', $model->goods_video):null;?>
                            <?= $form->field($model, 'goods_video')->widget(common\widgets\webuploader\Files::class, [
                                'type'=>'videos',
                                'config' => [
                                    'pick' => [
                                        'multiple' => true,
                                    ],
                                ]
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
var formID = 'purchaseapplygoodsform';
function searchGoods() {	
   var style_sn = $.trim($("#"+formID+"-style_sn").val());
   var jintuo_type = $("#"+formID+"-jintuo_type").val();
   if(!style_sn) {
	    rfMsg("请输入款号或起版号");
        return false;
   }
   var url = "<?= Url::buildUrl(\Yii::$app->request->url,[],['style_sn','search','jintuo_type'])?>&search=1&goods_sn="+style_sn+"&jintuo_type="+jintuo_type;
   window.location.href = url;
}
</script>
