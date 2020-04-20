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
                    'route' => 'indexStyle',
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
                                    'title' => '商品列表',
                                    'route' => 'style-goods/index',
                            ],
                            [
                                    'title' => '裸钻列表',
                                    'route' => 'diamond/index',
                            ],
                    ],
                    
            ],

            [
                'title' => '起版管理',
                'route' => 'indexQiban',
                'icon' => 'fa fa-superpowers',
                'child' => [

                    [
                        'title' => '有款起版',
                        'route' => 'style-qiban/index',
                    ],
                    [
                        'title' => '无款起版',
                        'route' => 'qiban/index',
                    ],

                ],

            ],

            [
                    'title' => '功能配置',
                    'route' => 'indexStyleConfig',
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
            'title' => '所有权限',
            'name' => '*',
        ],
    ],
];