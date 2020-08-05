<?php
use yii\widgets\ActiveForm;
$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <?php $form = ActiveForm::begin([]); ?>
            <div class="box-body" style="padding:20px 50px">
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'name')->textInput(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'cate')->dropDownList(\common\enums\FlowCateEnum::getMap(),['prompt'=>'请选择']) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'method')->dropDownList(\common\enums\FlowMethodEnum::getMap(),['prompt'=>'请选择']) ?>
                    </div>
                </div>
                <div>

                <div class="row">
                    <div class="col-lg-12">
<!--                        --><?php
//                        $users = [
//                            [
//                                'name'  => 'user_id',
//                                'type'  => \kartik\select2\Select2::class,
//                                'options' => [
//                                    'data'  =>Yii::$app->services->backendMember->getMap(),
//                                ],
//
//
//                            ]
//                        ];
//                        ?>
<!--                        --><?//= unclead\multipleinput\MultipleInput::widget([
//                            'name' => "users",
//                            'value' =>json_decode($model->users,true),
//                            'columns' => $users,
//                        ]); ?>

                        <?= $form->field($model, 'users')->widget(unclead\multipleinput\MultipleInput::class, [
                            'max' => 6,
                            'value' => $user_id_arr,
                            'allowEmptyList'=>false,
                            'enableGuessTitle'=>true,
                            'columns'=> [
                                [
                                    'name'  => 'user_id',
                                    'type'  => \kartik\select2\Select2::class,
                                    'options' => [
                                         'data'  =>Yii::$app->services->backendMember->getMap(),
                                     ],


                                ],
                            ]

                        ]);
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'status')->radioList(\common\enums\StatusEnum::getMap()); ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

