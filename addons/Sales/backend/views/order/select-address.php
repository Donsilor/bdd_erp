<?php

use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\helpers\Html;
use common\helpers\Url;

use addons\Style\common\enums\AttrIdEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = Yii::t('goods', 'flows');
//$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableAjaxValidation' => false,
            'validationUrl' => Url::to(['select-address', 'id' => $model->order_id]),
        ]); ?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body table-responsive">
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover'],
                    'id'=>'grid',
                    'columns' => [
                        [
                            'class'=>'yii\grid\RadioButtonColumn',
                            'name'=>'id',  //设置每行数据的复选框属性
                            'headerOptions' => ['width'=>'30'],
                        ],
                        [
                            'attribute' => 'id',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'realname',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'label' => '联系方式',
                            'attribute' => 'mobile',
                            'value' => function($model){
                                $str = '';
                                $str .= $model->mobile ? $model->mobile."<br/>":'';
                                $str .= $model->email ? $model->email."<br/>":'';
                                return $str;
                            },
                            'filter' => false,
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'country_name',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'province_name',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],
                        [
                            'attribute' => 'city_name',
                            'filter' => false,
                            'format' => 'raw',
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ], 
                        [
                                'attribute' => 'address_details',
                                'filter' => false,
                                'format' => 'raw',
                                'headerOptions' => ['class' => 'col-md-2'],
                        ],
                        [
                                'attribute' => 'zip_code',
                                'filter' => false,
                                'format' => 'raw',
                                //'headerOptions' => ['class' => 'col-md-1'],
                        ],     
                        [
                            'attribute'=>'created_at',
                            'value'=>function($model){
                                return Yii::$app->formatter->asDatetime($model->created_at);
                            },
                            'filter' => false,
                            //'headerOptions' => ['class' => 'col-md-1'],
                        ],                        
                    ]
                  ]);
                ?>                
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>