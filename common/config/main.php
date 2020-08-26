<?php
return [
    'name' => 'BDD ERP',
    'version' => '2.6.10',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'sourceLanguage' => 'zh-cn',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => [
        'queue', // 队列系统
        'common\components\Init', // 加载默认的配置8.129.190.33
    ],
    'components' => [
        'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=8.129.190.33;port=3389;dbname=bdd_erp;',
                'username' => 'super',
                'password' => 'Bdd123o123',
                'charset' => 'utf8',
                'tablePrefix' => '',
                'attributes' => [
                        // PDO::ATTR_STRINGIFY_FETCHES => false, // 提取的时候将数值转换为字符串
                        // PDO::ATTR_EMULATE_PREPARES => false, // 启用或禁用预处理语句的模拟
                ],
                // 'enableSchemaCache' => true, // 是否开启缓存, 请了解其中机制在开启，不了解谨慎
                // 'schemaCacheDuration' => 3600, // 缓存时间
                // 'schemaCache' => 'cache', // 缓存名称
        ],
        //BDD正式erp
        'db2' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=47.75.210.123;port=3306;dbname=bdd_erp;',
                'username' => 'super',
                'password' => 'Bdd123o123',
                'charset' => 'utf8',
                'tablePrefix'=>'',
                'attributes' => [

                ],
        ], 
        //BDD官网
        'bddDb' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=47.75.210.123;port=3306;dbname=bdd;',
                'username' => 'super',
                'password' => 'Bdd123o123',
                'charset' => 'utf8',
                'tablePrefix'=>'',
                'attributes' => [
                        
                ],
        ],
        /** ------ 缓存 ------ **/
        'cache' => [
            'class' => 'yii\redis\Cache',
            //'class' => 'yii\caching\FileCache',
            /**
             * 文件缓存一定要有，不然有可能会导致缓存数据获取失败的情况
             *
             * 注意如果要改成非文件缓存请删除，否则会报错
             */
            //'cachePath' => '@backend/runtime/cache'
        ],
        /** ------ 格式化时间 ------ **/
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
            'nullDisplay' => ''
        ],
        /** ------ 服务层 ------ **/
        'services' => [
            'class' => 'services\Application',
        ],
        /** ------ redis配置 ------ **/
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',            
            'port' => 6379,
            'database' => 3            
        ],
        /** ------ 网站碎片管理 ------ **/
        'debris' => [
            'class' => 'common\components\Debris',
        ],
        /** ------ 访问设备信息 ------ **/
        'mobileDetect' => [
            'class' => 'Detection\MobileDetect',
        ],
        /** ------ 队列设置 ------ **/
        'queue' => [
                'class' => yii\queue\redis\Queue::class,
                'as log' => yii\queue\LogBehavior::class,
                'redis' => 'redis', // 连接组件或它的配置
                'channel' => 'queue', // Queue channel key
                'ttr' => 1200, // Max time for job execution
                'attempts' => 3,  // Max number of attempts
        ],
        /** ------ 公用支付 ------ **/
        'pay' => [
            'class' => 'common\components\Pay',
        ],
        /** ------ 上传组件 ------ **/
        'uploadDrive' => [
            'class' => 'common\components\UploadDrive',
        ],
        /** ------ 快递查询 ------ **/
        'logistics' => [
            'class' => 'common\components\Logistics',
        ],
        /** ------ 二维码 ------ **/
        'qr' => [
            'class' => '\Da\QrCode\Component\QrCodeComponent',
            // ... 您可以在这里配置组件的更多属性
        ],
        /** ------ 微信SDK ------ **/
        /* 'wechat' => [
            'class' => 'common\components\Wechat',
            'userOptions' => [],  // 用户身份类参数
            'sessionParam' => 'wechatUser', // 微信用户信息将存储在会话在这个密钥
            'returnUrlParam' => '_wechatReturnUrl', // returnUrl 存储在会话中
            'rebinds' => [
                'cache' => 'common\components\WechatCache',
            ]
        ], */
        /** ------ i18n 国际化 ------ **/
        'i18n' => [
                'translations' => [
                        '*' => [
                                'class' => 'yii\i18n\PhpMessageSource',
                                'basePath' => '@app/languages',
                                'fileMap' => [
                                        
                                ],
                        ],
                ],
        ],
        'area' => ['class' => 'common\components\Area'],
		'attr' => ['class' => 'common\components\Attribute'],
        'trans'=> ['class'=>'common\components\Transaction'],
        'goldTool'=> ['class'=>'common\components\GoldTool'],
        'shopAttr'=> ['class'=>'common\components\ShopAttribute'],
        'jdSdk' => [
                'class' => 'common\components\JdSdk',
                'appKey' => '600FE385E066028D2A1B30C46C54E54C',
                'appSecret' => '1a693572f25440d8905672fac4611a6a',
                'refreshToken'=>'e5a3eb79e4b64dfd9efa4f42fd9883dfm2vi',
        ],
    ],
];
