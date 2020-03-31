<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\style\common\models\Style */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('goods', 'Style');
$this->params['breadcrumbs'][] = ['label' => Yii::t('goods', 'Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.nav-tabs-custom{
	
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                
                <h3 class="box-title">基本信息</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="row">
        		<div class="col-lg-4"><?= $form->field($model, 'style_sn')->textInput(['maxlength' => true])->label("商品名称") ?></div>
    			<div class="col-lg-4">
    				<div class="nav-tabs-custom">
    			        <?php echo Html::langTab("tab","款式名称")?>    			      
            			<div class="tab-content"> 
            				<?php 
                    			echo LangBox::widget(['form'=>$form,'model'=>$model,'fields'=>[
                    			    'style_name'=>['type'=>'textInput','options'=>['maxlength' => true],'label'=>false]
                    			]]);
                			?>
            			</div>
    			    </div>
    			</div>
    			<div class="col-lg-4"><?= $form->field($model, 'style_sn')->textInput(['maxlength' => true])->label("商品名称") ?></div>
			</div>
                 <ul class="nav nav-tabs">
                    <?php foreach (\Yii::$app->params['languages'] as $lang_key=>$lang_name){?>
                    <li class="<?php echo Yii::$app->language==$lang_key?"active":"" ?>">
                    		<a href="#tab_<?php echo $lang_key?>" data-toggle="tab" aria-expanded="false"><?php echo $lang_name?></a>
                    </li>
                    <?php }?>           
                </ul>            
                <div class="tab-content">  
           			<?php $newLangModel = $model->langModel();?>
              		<?php 
              		  foreach (\Yii::$app->params['languages'] as $lang_key=>$lang_name){
              		     $is_new = true;    
              		  ?>                        		
              		    <?php foreach ($model->langs as $langModel) {?>
                            <?php if($lang_key == $langModel->language){?>
                            	<!-- 编辑-->
                                <div class="tab-pane<?php echo Yii::$app->language==$lang_key?" active":"" ?>" id="tab_<?= $lang_key?>">
                                     <?= $form->field($langModel, 'style_name')->textInput(['name'=>Html::langInputName($langModel,$lang_key,"style_name")]) ?>
                              	</div>
								
                              	<!-- /.tab-pane -->
                            	<?php $is_new = false; break;?>
                            <?php }?>
                        <?php }?>
                        <?php if($is_new == true){?>
                        <!-- 新增 -->
                        <div class="tab-pane<?php echo Yii::$app->language==$lang_key?" active":"" ?>" id="tab_<?= $lang_key?>">
                               <?= $form->field($newLangModel, 'style_name')->textInput(['name'=>Html::langInputName($newLangModel,$lang_key,"style_name")]) ?>
                        </div>
                        <!-- /.tab-pane -->
                        <?php }?>                         
                    <?php }?>
                </div>    
                <div class="col-sm-12">
                    <?= $form->field($model, 'style_sn')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'cat_id')->dropDownList([]) ?>
                    <?= $form->field($model, 'type_id')->dropDownList([]) ?>
                    <?= $form->field($model, 'style_image')->widget(\common\widgets\webuploader\Files::class, [
                            'type' => 'images',
                            'theme' => 'default',
                            'themeConfig' => [],
                            'config' => [
                                // 可设置自己的上传地址, 不设置则默认地址
                                // 'server' => '',
                                'pick' => [
                                    'multiple' => true,
                                ],
                            ]
                    ]); ?>
                    <?= $form->field($model, 'goods_body')->widget(\common\widgets\ueditor\UEditor::class, []) ?>
                    <?= $form->field($model, 'mobile_body')->widget(\common\widgets\ueditor\UEditor::class, []) ?>
                    <?= $form->field($model, 'sale_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'storage_alarm')->textInput() ?>
                    <?= $form->field($model, 'status')->textInput() ?>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
