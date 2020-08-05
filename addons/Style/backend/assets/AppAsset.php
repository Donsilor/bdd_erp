<?php

namespace addons\Style\backend\assets;

use yii\web\AssetBundle;

/**
 * 静态资源管理
 *
 * Class AppAsset
 * @package addons\Style\backend\assets
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@addons/Style/backend/resources/';

    public $css = [
    ];

    public $js = [
    ];

    public $depends = [
    ];
}