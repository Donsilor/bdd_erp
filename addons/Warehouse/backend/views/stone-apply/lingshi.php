<?php
use yii\widgets\ActiveForm;
use common\helpers\Url;
use yii\grid\GridView;
use common\helpers\Html;
use addons\Supply\common\enums\FromTypeEnum;


$this->title = '领料单创建';
$this->params['breadcrumbs'][] = ['label' => 'Curd', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
 <?php $form = ActiveForm::begin([]); ?>  
<div class="row">
    <div class="col-xs-12">
        <div class="box" style="margin-bottom:1px">
            <div class="box-header" >
                <h3 class="box-title"><i class="fa fa-qrcode"></i> 单据信息</h3>
            </div>
             <div class="box-body table-responsive" style="padding-top: 0px;padding-bottom: 0px;">
                 <table class="table table-hover">
                     <tr>
                         <td class="col-xs-1 text-right">单据编号：</td>
                         <td>自动生成</td>
   						 <td class="col-xs-1 text-right">单据类型：</td>
   						 <td>领料单</td>
                         <td class="col-xs-1 text-right">加工商：</td>
                         <td>
                         <?php echo $model->supplier->supplier_name ??''?>
                         <?php echo Html::activeHiddenInput($model, "supplier_id");?>
                         </td>                         
                     </tr> 
                     <tr>
                         <td class="col-xs-1 text-right">来源单号：</td>
                         <td><?php echo $model->from_order_sn;?></td>
   						 <td class="col-xs-1 text-right">来源类型：</td>
   						 <td><?php echo FromTypeEnum::getValue($model->from_type);?></td>
                         <td class="col-xs-1 text-right">单据备注：</td>
                         <td><?php echo  Html::activeTextarea($model, "remark",['class' => 'form-control']);?></td>
                     </tr>                                                                           
                 </table>
             </div>
        </div>
        <div class="box">
             <div class="box-header" >
                  <h3 class="box-title"><i class="fa fa-qrcode"></i> 单据明细</h3>
             </div>
                     
            <div class="box-body" style="padding:20px 20px">
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => ['class' => 'table'],
                        'id'=>'grid',
                        'columns' => [
                            [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible' => true,
                                    'headerOptions' => ['style'=>'width:50px'],
                            ],
                            [
                                    'attribute' => 'id',
                                    'value'  => 'id',
                                    'filter' => false,
                                    'headerOptions' => ['style'=>'width:50px'],
                            ], 
                            [
                                    'label' => '布产编号',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:120px'],
                                    'value' => function($model) {
                                       return $model->produce_sn;
                                    },
                            
                            ],
                            [
                                    'attribute' => 'peishi_status',
                                    'value' => function ($model){
                                        return \addons\Supply\common\enums\PeishiStatusEnum::getValue($model->peishi_status);
                                    },
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                            ], 
                            [
                                    'label' => '石包编号',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                          $str = '';
                                          foreach ($model->stoneGoods ??[] as $stoneGoods){
                                              $str.= $stoneGoods->stone_sn.'<br/>';
                                          }
                                          return $str;
                                    },
                                    
                            ],
                            [
                                    'label' => '石料类型',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                        $str = '';
                                        foreach ($model->stoneGoods ??[] as $stoneGoods){
                                            $str.= Yii::$app->attr->valueName($stoneGoods->stone->stone_type??'').'<br/>';
                                        }
                                        return $str;
                                    },
                                    
                            ],
                            [
                                    'label' => '领料数量',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                        $str = '';
                                        foreach ($model->stoneGoods ??[] as $stoneGoods){
                                            $str.= ($stoneGoods->stone_num??'0').'<br/>';
                                        }
                                        return $str;
                                    },
                                    
                            ],
                            [
                                    'label' => '领料重量',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                        $str = '';
                                        foreach ($model->stoneGoods ??[] as $stoneGoods){
                                            $str.= ($stoneGoods->stone_weight??'0').'ct<br/>';
                                        }
                                        return $str;
                                    },
                                    
                            ],
                            [
                                    'label' => '石料颜色',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                        $str = '';
                                        foreach ($model->stoneGoods ??[] as $stoneGoods){
                                            $str.= Yii::$app->attr->valueName($stoneGoods->stone->stone_color??'').'<br/>';
                                        }
                                        return $str;
                                    },
                            
                            ],
                            [
                                    'label' => '石料净度',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                        $str = '';
                                        foreach ($model->stoneGoods ??[] as $stoneGoods){
                                            $str.= Yii::$app->attr->valueName($stoneGoods->stone->stone_clarity??'').'<br/>';
                                        }
                                        return $str;
                                    },
                                    
                            ],
                            [
                                    'label' => '石料切工',
                                    'filter' => false,
                                    'format' => 'raw',
                                    'headerOptions' => ['style'=>'width:100px'],
                                    'value' => function($model) {
                                    
                                    },
                                    
                            ],   
                        ]
                    ]); ?>
            </div>
      
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>