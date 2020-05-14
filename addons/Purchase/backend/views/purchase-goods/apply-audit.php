<?php

use common\helpers\RegularHelper;

$this->title = '系统信息';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

$prefix = !RegularHelper::verify('url', Yii::getAlias('@attachurl')) ? Yii::$app->request->hostInfo : '';

?>

<div class="row">
    <div class="col-xs-7">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-cog"></i> 环境配置</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td>PHP版本</td>
                        <td><?= phpversion(); ?></td>
                    </tr>
                    <tr>
                        <td>Mysql版本</td>
                        <td><?= Yii::$app->db->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION); ?></td>
                    </tr>
                    <tr>
                        <td>解析引擎</td>
                        <td><?= $_SERVER['SERVER_SOFTWARE']; ?></td>
                    </tr>
                    <tr>
                        <td>数据库大小</td>
                        <td><?= Yii::$app->formatter->asShortSize($mysql_size, 2); ?></td>
                    </tr>
                    <tr>
                        <td>附件目录</td>
                        <td><?= $prefix . Yii::getAlias('@attachurl'); ?>/</td>
                    </tr>
                    <tr>
                        <td>附件目录大小</td>
                        <td><?= Yii::$app->formatter->asShortSize($attachment_size, 2); ?></td>
                    </tr>
                    <tr>
                        <td>超时时间</td>
                        <td><?= ini_get('max_execution_time'); ?>秒</td>
                    </tr>
                    <tr>
                        <td>客户端信息</td>
                        <td><?= $_SERVER['HTTP_USER_AGENT'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-5">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-code"></i> 系统信息</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <tr>
                        <td>系统全称</td>
                        <td><?= Yii::$app->params['exploitFullName']; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>重量级全栖框架，为二次开发而生。</td>
                    </tr>
                    <tr>
                        <td>系统版本</td>
                        <td><?= Yii::$app->version; ?></td>
                    </tr>
                    <tr>
                        <td>Yii2版本</td>
                        <td><?= Yii::getVersion(); ?><?php if (YII_DEBUG) echo ' (开发模式)'; ?></td>
                    </tr>
                    <tr>
                        <td>官网</td>
                        <td><?= Yii::$app->params['exploitOfficialWebsite']?></td>
                    </tr>
                    <tr>
                        <td>官方QQ群</td>
                        <td><a href="https://jq.qq.com/?_wv=1027&amp;k=4BeVA2r" target="_blank">655084090</a></td>
                    </tr>
                    <tr>
                        <td>GitHub</td>
                        <td><?= Yii::$app->params['exploitGitHub']?></td>
                    </tr>
                    <tr>
                        <td>开发者</td>
                        <td><?= Yii::$app->params['exploitDeveloper']?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-lemon-o"></i> 采购布产编辑审核</h3>
            </div>
            <div class="box-body table-responsive">
                
            </div>
        </div>
    </div>
    
</div>