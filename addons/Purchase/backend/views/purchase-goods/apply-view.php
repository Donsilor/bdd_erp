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
                    <tr>
                        <td class="col-xs-2 text-right">PHP版本：</td>
                        <td>1111</td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right">Mysql版本：</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right">解析引擎：</td>
                        <td></td>
                    </tr>                    
                    <tr>
                        <td class="col-xs-2 text-right">附件目录：</td>
                        <td></td>
                    </tr>                    
                    <tr>
                        <td class="col-xs-2 text-right">超时时间：</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right">客户端信息：</td>
                        <td></td>
                    </tr>
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
                    <tr>
                        <td class="col-xs-2 text-right">PHP版本：</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right">Mysql版本：</td>
                        <td class="red">123123</td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right">解析引擎：</td>
                        <td class="red"><?= $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>                    
                    <tr>
                        <td class="col-xs-2 text-right">附件目录：</td>
                        <td></td>
                    </tr>                    
                    <tr>
                        <td class="col-xs-2 text-right">超时时间：</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2 text-right">客户端信息：</td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>    
</div>