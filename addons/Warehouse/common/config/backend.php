<?php

return [

    // ----------------------- 菜单配置 ----------------------- //
    'config' => [
        // 菜单配置
        'menu' => [
            'location' => 'default', // default:系统顶部菜单;addons:应用中心菜单
            'icon' => 'fa fa-puzzle-piece',
        ],
        // 子模块配置
        'modules' => [
        ],
    ],

    // ----------------------- 快捷入口 ----------------------- //

    'cover' => [

    ],

    // ----------------------- 菜单配置 ----------------------- //

    'menu' => [
            [
                'title' => '仓储管理',
                'route' => 'indexWarehouse',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                            'title' => '商品列表',
                            'route' => 'warehouse-goods/index',
                    ],
                ],

            ],
            [
                'title' => '单据管理',
                'route' => 'indexWarehouseBill',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                            'title' => '单据列表',
                            'route' => 'bill/index',
                    ],
                    [
                            'title' => '入库收货单',
                            'route' => 'bill-l/index',

                    ],
                    [
                            'title' => '调拨单',
                            'route' => 'bill-m/index',
                    ],
                    [
                            'title' => '盘点单',
                            'route' => 'warehouse-bill-w/index',
                    ],
                    /*[
                            'title' => '维修退货单',
                            'route' => 'warehouse-bill-o/index',
                    ],
                    [
                            'title' => '维修调拨单',
                            'route' => 'warehouse-bill-wf/index',
                    ],
                    [
                            'title' => '维修发货单',
                            'route' => 'warehouse-bill-r/index',
                    ],*/
                    [

                            'title' => '仓库维修单',
                            'route' => 'bill-repair/index',
                    ],
                    [
                            'title' => '退货返厂单',
                            'route' => 'bill-b/index',
                    ],
                ],

            ],
            [
                    'title' => '石包管理',
                    'route' => 'indexStone',
                    'icon' => 'fa fa-superpowers',
                    'child' => [
                        [
                            'title' => '石包列表',
                            'route' => 'stone/index',
                        ],
                        [
                            'title' => '买石单',
                            'route' => 'stone-bill-ms/index',
                        ],
                        [
                            'title' => '送石单',
                            'route' => 'stone-bill-ss/index',
                        ],
                        [
                            'title' => '退石单',
                            'route' => 'stone-bill-ts/index',
                        ],
                    ],

            ],
            [
                'title' => '功能配置',
                'route' => 'indexWarehouseConfig',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                            'title' => '仓库管理',
                            'route' => 'warehouse/index',
                    ],
                    [
                            'title' => '柜位管理',
                            'route' => 'warehouse-box/index',
                    ]
                ],

            ],

    ],

    // ----------------------- 权限配置 ----------------------- //

    'authItem' => [
        [
            'title' => '所有权限',
            'name' => '*',
        ],
    ],
];