<?php

use common\helpers\Html;

$this->title = '采购编辑审批';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];
?>
<div class="row">
<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-cog"></i> 采购布产编辑-审批</h3>                         
        </div>
        <div class="box-body table-responsive">
             <table class="table table-hover">
                    <tr>
                        <td class="col-xs-1 text-right">采购单号：</td>
                        <td>1111</td>
                        <td class="col-xs-1 text-right">布产单号：</td>
                        <td>4234</td>
                        <td class="col-xs-1 text-right">布产状态：</td>
                        <td>423423</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-center">
                        <?= Html::edit(['ajax-audit','id'=>1], '审  批', [
                             'class'=>'btn btn-success btn-sm',
                             'data-toggle' => 'modal',
                             'data-target' => '#ajaxModal',
                         ]);?>
                         <span class="btn btn-white" onclick="window.location.href='<?php echo $returnUrl;?>'">返回</span>
                        </td>                       
                    </tr>
                </table>
        </div>
    </div>
</div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 修改前</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                  <?php if($model->apply_info) { ?>
                       <?php foreach ($model->apply_info as $info) {?>
                        <tr>
                            <td class="col-xs-2 text-right"><?php echo $info['label']?>：</td>
                            <td><?php echo $info['org_value']?></td>
                        </tr>
                       <?php }?>
                  <?php }?>                   
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info"></i> 修改后</h3>
            </div>
            <div class="box-body table-responsive">
                 <table class="table table-hover">
                    <?php if($model->apply_info) {?>
                       <?php foreach ($model->apply_info as $info) {?>
                        <tr>
                            <td class="col-xs-2 text-right"><?php echo $info['label']?>：</td>
                            <td<?php echo $info['changed'] ?' class="red"':'';?>><?php echo $info['value']?></td>
                        </tr>
                       <?php }?>
                  <?php }?>
                </table>
            </div>
        </div>
    </div>    
</div>