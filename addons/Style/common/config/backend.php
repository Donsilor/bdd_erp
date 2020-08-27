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
                    'title' => '款式管理',
                    'route' => 'style',
                    'icon' => 'fa fa-superpowers',
                    'child' => [                            
                            [
                                    'title' => '款式列表',
                                    'route' => 'style/index',
                            ],
                            [
                                    'title' => '起版列表',
                                    'route' => 'qiban/index',
                            ],
                            [
                                    'title' => '待起版列表',
                                    'route' => 'qiban/apply',
                            ],
                            [
                                    'title' => '商品列表',
                                    'route' => 'style-goods/index',
                            ],
                            [
                                    'title' => '裸钻列表',
                                    'route' => 'diamond/index',
                            ],
                            [
                                    'title' => '赠品款式',
                                    'route' => 'style-gift/index',
                            ],
                    ],
                    
            ],
            [
                    'title' => '原料管理',
                    'route' => 'material_style',
                    'icon' => 'fa fa-superpowers',
                    'child' => [
                            [
                                    'title' => '金料款式',
                                    'route' => 'gold-style/index',
                            ],
                            [
                                    'title' => '石料款式',
                                    'route' => 'stone-style/index',
                            ],
                            [
                                    'title' => '配件款式',
                                    'route' => 'parts-style/index',
                            ],
                    ],
            ],
            [
                    'title' => '功能配置',
                    'route' => 'function',
                    'icon' => 'fa fa-superpowers',
                    'child' => [
                            [
                                    'title' => '产品属性',
                                    'route' => 'attribute/index',
                            ],
                            [
                                    'title' => '产品分类',
                                    'route' => 'style-cate/index',
                            ],
                            [
                                    'title' => '产品线',
                                    'route' => 'product-type/index',
                            ],
                            [
                                    'title' => '产品规格',
                                    'route' => 'attribute-spec/index',
                            ],
                            [
                                    'title' => '款式渠道',
                                    'route' => 'style-channel/index',
                            ],
                            [
                                    'title' => '款式来源',
                                    'route' => 'style-source/index',
                            ],
                            [
                                    'title' => '金损配置',
                                    'route' => 'gold-loss-rate/index',
                            ],
                            [
                                    'title' => '材质税率信息',
                                    'route' => 'material-tax/index',
                            ],
                            [
                                    'title' => '毛利率配置',
                                    'route' => 'profit-rate/index',
                            ],



                    ],
                    
            ],
    ],

    // ----------------------- 权限配置 ----------------------- //

    'authItem' => [
        [
            'title' => '款式管理',
            'name' => 'style',
            'child' => [
                   [
                       'title' =>'款式列表',
                       'name'  => 'style/index',
                       'child' =>[
                               ['title' => '新增/编辑','name'  => 'style/ajax-edit'],
                               ['title' => '启用/禁用','name'  => 'style/ajax-update'],
                               ['title' => '提交审核','name'  => 'style/ajax-apply'],                           
                               ['title' => '审核','name'  => 'style/ajax-audit'],                           
                               ['title' => '详情','name'  => 'style/view'],
                               
                               ['title' => '款式属性(*)','name'  => 'style-attribute/*'],
                               ['title' => '款式商品(*)','name'  => 'style-goods/*'],
                               ['title' => '石头信息(*)','name'  => 'style-stone/*'],
                               ['title' => '工厂信息(*)','name'  => 'style-factory/*'],
                               ['title' => '工费信息(*)','name'  => 'style-factory-fee/*'],
                               ['title' => '款式图片(*)','name'  => 'style-image/*'],
                       ],
                 ],
                 [
                        'title' =>'起版列表',
                        'name'  => 'qibanIndex',
                        'child' =>[
                                ['title' => '首页','name'  => 'qiban/index'],
                                ['title' => '新增/编辑(有款)','name'  => 'qiban/edit'],
                                ['title' => '新增/编辑(无款)','name'  => 'qiban/edit-no-style'],
                                ['title' => '启用/禁用','name'  => 'qiban/ajax-update'],
                                ['title' => '提交审核','name'  => 'qiban/ajax-apply'],
                                ['title' => '审核','name'  => 'qiban/ajax-audit'],
                                ['title' => '详情','name'  => 'qiban/view'],                                
                        ],
                ],
                [
                        'title' =>'商品列表',
                        'name'  => 'styleGoodsIndex',
                        'child' =>[
                                ['title' => '首页','name'  => 'style-goods/index'],
                                ['title' => '编辑','name'  => 'style-goods/edit-all'],
                                ['title' => '详情','name'  => 'style-goods/view'],
                        ],
                ],
                [
                        'title' =>'裸钻列表',
                        'name'  => 'diamondIndex',
                        'child' =>[
                                ['title' => '首页','name'  => 'diamond/index'],
                                ['title' => '新增/编辑','name'  => 'diamond/edit'],
                                ['title' => '启用/禁用','name'  => 'diamond/ajax-update'],
                                ['title' => '提交审核','name'  => 'diamond/ajax-apply'],
                                ['title' => '审核','name'  => 'diamond/ajax-audit'],
                                ['title' => '详情','name'  => 'diamond/view'],
                        ],
                ],
            ],
        ],
        [    
            'title' => '功能配置',
            'name' => 'function',
            'child' => [
                    ['title' => '产品属性(*)','name'  => 'attribute/*'],
                    ['title' => '产品分类(*)','name'  => 'style-cate/*'],
                    ['title' => '产品线(*)','name'  => 'product-type/*'],
                    ['title' => '产品规格(*)','name'  => 'attribute-spec/*'],
                    ['title' => '款式渠道','name'  => 'style-channel/index','child' =>[
                            ['title' => '首页','name'  => 'style-channel/index'],
                            ['title' => '新增/编辑','name'  => 'style-channel/ajax-edit'],
                            ['title' => '启用/禁用','name'  => 'style-channel/ajax-update'],
                    ]],
                    ['title' => '款式来源','name'  => 'style-source/index','child' =>[
                            ['title' => '首页','name'  => 'style-source/index'],
                            ['title' => '新增/编辑','name'  => 'style-source/ajax-edit'],
                            ['title' => '启用/禁用','name'  => 'style-source/ajax-update'],
                    ]],
                    ['title' => '金损配置','name'  => 'gold-loss-rate/index','child' =>[
                            ['title' => '首页','name'  => 'gold-loss-rate/index'],
                            ['title' => '新增/编辑','name'  => 'gold-loss-rate/ajax-edit'],
                            ['title' => '启用/禁用','name'  => 'gold-loss-rate/ajax-update'],
                    ]],
                    ['title' => '材质税率配置','name'  => 'material-tax/index','child' =>[
                            ['title' => '首页','name'  => 'material-tax/index'],
                            ['title' => '新增/编辑','name'  => 'material-tax/ajax-edit'],
                            ['title' => '启用/禁用','name'  => 'material-tax/ajax-update'],
                    ]],
                    ['title' => '毛利率配置','name'  => 'profit-rate/index','child' =>[
                            ['title' => '首页','name'  => 'profit-rate/index'],
                            ['title' => '新增/编辑','name'  => 'profit-rate/ajax-edit'],
                            ['title' => '启用/禁用','name'  => 'profit-rate/ajax-update'],
                    ]],
            ],
        ],
    ],
];