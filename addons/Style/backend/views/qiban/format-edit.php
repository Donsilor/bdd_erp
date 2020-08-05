<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use addons\Style\common\enums\StyleSexEnum;
use addons\Style\common\enums\QibanTypeEnum;

$this->title = '版式编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-12">
            <div class="box">
                <?php $form = ActiveForm::begin([]); ?>
                <div class="box-body" style="padding:20px 50px">
                        <div class="row">
                            <div class="col-lg-4">
                                <?= $form->field($model, 'format_sn')->textInput() ?>
                            </div>

                        </div>

                        <div class="row col-lg-12">
                            <div class="col-lg-6" style="padding: 0px;">
                                <?php $model->format_images = !empty($model->format_images)?explode(',', $model->format_images):null;?>
                                <?= $form->field($model, 'format_images')->widget(common\widgets\webuploader\Files::class, [
                                    'config' => [
                                        'theme'=>'default',
                                        'pick' => [
                                            'multiple' => true,
                                        ],
                                    ]
                                ]); ?>
                            </div>
                            <div class="col-lg-6" style="padding: 0px;">
                                <?php $model->format_video = !empty($model->format_video)?explode(',', $model->format_video):null;?>
                                <?= $form->field($model, 'format_video')->widget(common\widgets\webuploader\Files::class, [
                                    'type'=>'videos',
                                    'config' => [
                                        'pick' => [
                                            'multiple' => true,
                                        ],
                                    ]
                                ]); ?>
                            </div>

                        </div>


                        <div class="row col-lg-12" >
                            <h3 class="box-title"> 工艺信息</h3>
                        </div>
                        <div class="row col-lg-12">
                            <div class="box-body table-responsive">
                                <div class="tab-content">
                                    <?php
                                        $format_info = [
                                            [
                                                'name' =>'format_craft_type',
                                                'title'=>"特殊工艺",
                                                'type'  => 'dropDownList',
                                                'options' => [
                                                    'class' => 'input-priority',
                                                    'style'=>'width:120px',
                                                    'prompt'=>'请选择',
                                                ],
                                                'items' => \addons\Purchase\common\enums\SpecialCraftEnum::getMap()
                                            ],
                                            [
                                                'name' =>'format_craft_desc',
                                                'title'=>"工艺描述",
                                                'type'  => 'textArea',
                                                'defaultValue' => '',
                                                'options' => [
                                                    'class' => 'input-priority',
                                                    'style'=>'min-width:200px;height:80px',
                                                ],
                                            ],
                                            [
                                                'name' =>'format_craft_images',
                                                'title'=>'工艺图片',
                                                'type'=> common\widgets\webuploader\MultipleFiles::class,
                                                'options'=>[
                                                    'config' => [
                                                        'pick' => [
                                                            'multiple' => true,
                                                        ],
                                                    ],

                                                ]

                                            ]
                                        ];
                                    ?>
                                    <?= unclead\multipleinput\MultipleInput::widget([
                                        'name' => "format_info",
                                        'value' =>json_decode($model->format_info,true),
                                        'columns' => $format_info,
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    <div class="col-lg-12">
                        <?= $form->field($model, 'format_remark')->textarea() ?>
                    </div>

                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>