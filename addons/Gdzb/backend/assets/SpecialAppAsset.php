<?php

namespace addons\Gdzb\backend\assets;

use yii\web\AssetBundle;

/**
 * 静态资源管理
 *
 * Class AppAsset
 * @package addons\Gdzb\backend\assets
 */
class SpecialAppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@addons/Gdzb/backend/resources/special';

    public $css = [
        'css/index.css'
    ];

    public $js = [
        'js/jquery.js',
        'js/index.js'
    ];

    public $depends = [
    ];
}