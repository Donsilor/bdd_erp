<?php
namespace common\widgets\skutable\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package common\widgets\webuploader\assets
 */
class AppAsset extends AssetBundle {

    public $sourcePath = '@common/widgets/skutable/resources/';

    // css会自动载入
    public $css = [
            'css/skutable.css',
    ];

    public $js = [
        'js/createSkuTable.js',
        //'js/customSku.js',
        //'plugins/layer/layer.js'
    ];

    public $publishOptions = [
        'except' => [
            'php/',
            'index.html',
            '.gitignore'
        ]
    ];

    public $depends = [
    ];
}