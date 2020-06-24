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
                        'title' => '库存列表',
                        'route' => 'warehouse-goods/index',
                    ],
                    [
                        'title' => '入库单',
                        'route' => 'bill-l/index',
                    ],
                    [
                        'title' => '调拨单',
                        'route' => 'bill-m/index',
                    ],
                    [
                        'title' => '盘点单',
                        'route' => 'bill-w/index',
                    ],
                    /*[
                        'title' => '维修退货单',
                        'route' => 'bill-o/index',
                    ],
                    [
                        'title' => '维修调拨单',
                        'route' => 'bill-wf/index',
                    ],
                    [
                        'title' => '维修发货单',
                        'route' => 'bill-r/index',
                    ],*/
                    [
                        'title' => '维修出库单',
                        'route' => 'bill-repair/index',
                    ],
                    [
                        'title' => '退货返厂单',
                        'route' => 'bill-b/index',
                    ],
                    [
                        'title' => '其他收货单',
                        'route' => 'bill-t/index',
                    ],
                    [
                        'title' => '其他出库单',
                        'route' => 'bill-c/index',
                    ],
                    [
                        'title' => '调整单',
                        'route' => 'bill-a/index',
                    ],
                    [
                        'title' => '单据列表',
                        'route' => 'bill/index',
                    ],
                ],

            ],
            [
                'title' => '金料管理',
                'route' => 'indexGold',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                        'title' => '金料列表',
                        'route' => 'gold/index',
                    ],
                    [
                        'title' => '配料列表',
                        'route' => 'gold-apply/index',
                    ],
                    [
                        'title' => '入库单',
                        'route' => 'gold-bill-l/index',
                    ],
                    [
                        'title' => '领料单',
                        'route' => 'gold-bill-c/index',
                    ],
                    [
                        'title' => '盘点单',
                        'route' => 'gold-bill-w/index',
                    ],
                    [
                        'title' => '单据列表',
                        'route' => 'gold-bill/index',
                    ],
                ],
            ],
            [
                'title' => '石料管理',
                'route' => 'indexStone',
                'icon' => 'fa fa-superpowers',
                'child' => [
                    [
                        'title' => '石料列表',
                        'route' => 'stone/index',
                    ],
                    [
                         'title' => '配石列表',
                         'route' => 'stone-apply/index',
                    ],
                    [
                        'title' => '入库单',
                        'route' => 'stone-bill-ms/index',
                    ],
                    [
                        'title' => '领石单',
                        'route' => 'stone-bill-ss/index',
                    ],
                    [
                        'title' => '工厂退石单',
                        'route' => 'stone-bill-ts/index',
                    ],
                    [
                        'title' => '盘点单',
                        'route' => 'stone-bill-w/index',
                    ],
                    [
                        'title' => '单据列表',
                        'route' => 'stone-bill/index',
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