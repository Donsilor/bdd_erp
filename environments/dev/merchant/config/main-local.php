<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'BDDSS0jVuHUynRD3GGQ9ok6k1UK9glN9d',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => \common\components\gii\crud\Generator::class,
                'templates' => [
                    'rageframe' => '@common/components/gii/crud/merchant',
                    'default' => '@vendor/yiisoft/yii2-gii/src/generators/crud/default',
                ]
            ],
            'model' => [
                'class' => \yii\gii\generators\model\Generator::class,
                'templates' => [
                    'rageframe' => '@common/components/gii/model/merchant',
                    'default' => '@vendor/yiisoft/yii2-gii/src/generators/model/default',
                ]
            ],
        ],
    ];
}

return $config;
