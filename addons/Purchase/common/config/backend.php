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
            'title' => '商品采购',
            'route' => 'indexPurchase',
            'icon' => 'fa fa-superpowers',
            'child' => [ 
                        [
                                'title' => '采购申请单',
                                'route' => 'purchase-apply/index',
                        ],
                        [
                                'title' => '采购订单',
                                'route' => 'purchase/index',
                        ],
                        [
                                'title' => '采购收货单',
                                'route' => 'receipt/index',
                        ],
                        [
                                'title' => '不良返厂单',
                                'route' => 'defective/index',
                        ],
                        [
                                'title' => '质检列表',
                                'route' => 'receipt-goods/iqc-index',
                        ],
            ],

        ],
        [
                'title' => '金料采购',
                'route' => 'indexPurchase',
                'icon' => 'fa fa-superpowers',
                'child' => [
                        [
                                'title' => '金料采购单',
                                'route' => 'purchase-gold/index',
                        ],
                        [
                                'title' => '金料收货单',
                                'route' => 'gold-receipt/index',
                        ],
                        [
                                'title' => '金料返厂单',
                                'route' => 'gold-defective/index',
                        ],
                        [
                                'title' => '金料质检列表',
                                'route' => 'gold-receipt-goods/iqc-index',
                        ],
                ],
                
        ],
        [
                'title' => '石料采购',
                'route' => 'indexPurchase',
                'icon' => 'fa fa-superpowers',
                'child' => [
                        [
                                'title' => '石料采购单',
                                'route' => 'purchase-stone/index',
                        ],
                        [
                                'title' => '石料收货单',
                                'route' => 'stone-receipt/index',
                        ],
                        [
                                'title' => '供应商退石单',
                                'route' => 'stone-defective/index',
                        ],
                        [
                                'title' => '石料质检列表',
                                'route' => 'stone-receipt-goods/iqc-index',
                        ],
                ],
        ],
        [
                'title' => '配件采购',
                'route' => 'indexPurchase',
                'icon' => 'fa fa-superpowers',
                'child' => [
                        [
                                'title' => '配件采购单',
                                'route' => 'purchase-parts/index',
                        ],
                        [
                                'title' => '配件收货单',
                                'route' => 'parts-receipt/index',
                        ],
                        [
                                'title' => '配件返厂单',
                                'route' => 'parts-defective/index',
                        ],
                        [
                                'title' => '配件质检列表',
                                'route' => 'parts-receipt-goods/iqc-index',
                        ],
                ],
        ],
        [
                'title' => '赠品采购',
                'route' => 'indexPurchase',
                'icon' => 'fa fa-superpowers',
                'child' => [
                        [
                                'title' => '赠品采购单',
                                'route' => 'purchase-gift/index',
                        ],
                        [
                                'title' => '赠品收货单',
                                'route' => 'gift-receipt/index',
                        ],
                ],
        ],
        [
                'title' => '功能配置',
                'route' => 'indexPurchaseConfig',
                'icon' => 'fa fa-superpowers',
                'child' => [
                        [
                                'title' => '质检未过原因',
                                'route' => 'fqc-config/index',
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