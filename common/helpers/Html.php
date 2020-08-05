<?php

namespace common\helpers;

use common\enums\MethodEnum;
use Yii;
use yii\helpers\BaseHtml;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use common\enums\MessageLevelEnum;

/**
 * Class Html
 * @package common\helpers
 * @author jianyan74 <751393839@qq.com>
 */
class Html extends BaseHtml
{
    /**
     * 创建
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public static function create(array $url, $content = '创建', $options = [])
    {
        $options = ArrayHelper::merge([
            'class' => "btn btn-primary btn-xs"
        ], $options);

        $content = '<i class="icon ion-plus"></i> ' . $content;
        return self::a($content, $url, $options);
    }

    /**
     * 编辑
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public static function edit(array $url, $content = '编辑', $options = [])
    {
        $options = ArrayHelper::merge([
            'class' => 'btn btn-primary btn-sm',
        ], $options);

        return self::a($content, $url, $options);
    }

    /**
     * 删除
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public static function delete(array $url, $content = '删除', $options = [])
    {
        $options = ArrayHelper::merge([
            'class' => 'btn btn-danger btn-sm',
            'onclick' => "rfDelete(this);return false;"
        ], $options);

        return self::a($content, $url, $options);
    }

    /**
     * 普通按钮
     *
     * @param $url
     * @param array $options
     * @return string
     */
    public static function linkButton(array $url, $content, $options = [])
    {
        $options = ArrayHelper::merge([
            'class' => "btn btn-white btn-sm"
        ], $options);

        return self::a($content, $url, $options);
    }

    /**
     * 状态标签
     *
     * @param int $status
     * @return mixed
     */
    public static function status($status = 1, $options = [])
    {
        if (!self::beforVerify('ajax-update')) {
            return '';
        }

        $listBut = [
            StatusEnum::DISABLED => self::tag('span', '启用', array_merge(
                [
                    'class' => "btn btn-success btn-sm",
                    'data-toggle' => 'tooltip',
                    'data-original-title' => '点击启用',
                    'onclick' => "rfStatus(this)"
                ],
                $options
            )),
            StatusEnum::ENABLED => self::tag('span', '禁用', array_merge(
                [
                    'class' => "btn btn-default btn-sm",
                    'data-toggle' => 'tooltip',
                    'data-original-title' => '点击禁用',
                    'onclick' => "rfStatus(this)"
                ],
                $options
            )),
        ];

        return $listBut[$status] ?? '';
    }

    /**
     * @param string $text
     * @param null $url
     * @param array $options
     * @return string
     */
    public static function a($text, $url = null, $options = [])
    {
        if ($url !== null) {
            // 权限校验
            if (!self::beforVerify($url)) {
                return '';
            }

            $options['href'] = Url::to($url);
        }

        return static::tag('a', $text, $options);
    }

    /**
     * 排序
     *
     * @param $value
     * @return string
     */
    public static function sort($value, $options = [])
    {
        // 权限校验
        if (!self::beforVerify('ajax-update')) {
            return $value;
        }

        $options = ArrayHelper::merge([
            'class' => 'form-control',
            'onblur' => 'rfSort(this)',
        ], $options);

        return self::input('text', 'sort', $value, $options);
    }


    /**
     * 列表input更新字段
     *
     * @param $value
     * @return string
     */
    public static function ajaxInput($name,$value, $options = [])
    {
        $url = isset($options['data-url']) ?? 'ajax-update';
        // 权限校验
        if (!self::beforVerify($url)) {
            return $value;
        }
        $options = ArrayHelper::merge([
            'data-url' => $url,
            'class' => 'form-control',
            'onblur' => 'rfAjaxUpdate(this)',
        ], $options);

        return self::input('text', $name, $value, $options);
    }

    /**
     * 列表select更新字段
     *
     * @param $value
     * @return string
     */
    public static function ajaxSelect($model, $name, $items, $options = [])
    {
        $url = isset($options['ajax-update']) ?? 'ajax-update';
        // 权限校验
        if (!self::beforVerify($url)) {
            return $model->$name;
        }
        $options = ArrayHelper::merge([
            'ajax-update' => $url,
            'name' => $name,
            'class' => 'form-control',
            'onchange' => 'rfAjaxUpdate(this)',
        ], $options);

        return self::activeDropDownList($model, $name, $items, $options);
    }

    /**
     * 批量操作弹框
     *
     * @param $url
     * @param array $options
     * @param string $content
     * @return string
     */
    public static function batchPopButton(array $url, $content, $options = [])
    {
        $options = ArrayHelper::merge([
            'class' => "btn btn-success btn-sm",
            'onclick' => "batchPop(this);return false;"
        ], $options);

        return self::a($content, $url, $options);
    }

    /**
     * 批量填充按钮
     *
     * @param $url
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function batchFullButton(array $url, $content = "批量填充", $options = [])
    {
        $options = ArrayHelper::merge([
            'data-grid'=>'grid',
            'class' => "btn btn-info btn-xs",
            'onclick' => "rfBatchFull(this);return false;"
        ], $options);

        return self::a($content, $url, $options);
    }

    /**
     * 是否标签
     *
     * @param int $status
     * @return mixed
     */
    public static function whether($status = 1)
    {
        $listBut = [
            WhetherEnum::ENABLED => self::tag('span', '是', [
                'class' => "label label-primary label-sm",
            ]),
            WhetherEnum::DISABLED => self::tag('span', '否', [
                'class' => "label label-default label-sm",
            ]),
        ];

        return $listBut[$status] ?? '';
    }

    /**
     * 级别标签
     *
     * @param $level
     * @return mixed|string
     */
    public static function messageLevel($level)
    {
        $listBut = [
            MessageLevelEnum::INFO => self::tag('span', MessageLevelEnum::getValue(MessageLevelEnum::INFO), [
                'class' => "label label-info label-sm",
            ]),
            MessageLevelEnum::WARNING => self::tag('span', MessageLevelEnum::getValue(MessageLevelEnum::WARNING), [
                'class' => "label label-warning label-sm",
            ]),
            MessageLevelEnum::ERROR => self::tag('span', MessageLevelEnum::getValue(MessageLevelEnum::ERROR), [
                'class' => "label label-danger label-sm",
            ]),
        ];

        return $listBut[$level] ?? '';
    }

    /**
     * 方法判断标签
     *
     * @param $method
     * @return mixed|string
     */
    public static function method($method)
    {
        $listBut = [
            MethodEnum::GET => self::tag('span', MethodEnum::getValue(MethodEnum::GET), [
                'class' => "label label-success label-sm",
            ]),
            MethodEnum::POST => self::tag('span', MethodEnum::getValue(MethodEnum::POST), [
                'class' => "label label-info label-sm",
            ]),
            MethodEnum::PUT => self::tag('span', MethodEnum::getValue(MethodEnum::PUT), [
                'class' => "label label-primary label-sm",
            ]),
            MethodEnum::DELETE => self::tag('span', MethodEnum::getValue(MethodEnum::DELETE), [
                'class' => "label label-danger label-sm",
            ]),
            MethodEnum::ALL => self::tag('span', MethodEnum::getValue(MethodEnum::ALL), [
                'class' => "label label-warning label-sm",
            ]),
        ];

        return $listBut[$method] ?? '';
    }

    /**
     * 根据开始时间和结束时间发回当前状态
     *
     * @param int $start_time 开始时间
     * @param int $end_time 结束时间
     * @return mixed
     */
    public static function timeStatus($start_time, $end_time)
    {
        $time = time();
        if ($start_time > $end_time) {
            return "<span class='label label-danger'>有效期错误</span>";
        } elseif ($start_time > $time) {
            return "<span class='label label-default'>未开始</span>";
        } elseif ($start_time < $time && $end_time > $time) {
            return "<span class='label label-primary'>进行中</span>";
        } elseif ($end_time < $time) {
            return "<span class='label label-default'>已结束</span>";
        }

        return false;
    }

    /**
     * 由于ajax加载model有些控件会重新载入样式导致基础样式失调做的修复
     *
     * @return string|void
     */
    public static function modelBaseCss()
    {
        echo Html::cssFile(Yii::getAlias('@web') . '/resources/css/rageframe.css?v=' . time());

        Yii::$app->controller->view->registerCss(<<<Css
.modal {
    z-index: 999;
}

.modal-backdrop {
    z-index: 998;
}

Css
        );
    }

    /**
     * @param $route
     * @return bool
     */
    protected static function beforVerify($route)
    {
        // 未登录直接放行
        if (Yii::$app->user->isGuest) {
            return true;
        }

        is_array($route) && $route = $route[0];

        $route = Url::getAuthUrl($route);
        substr("$route", 0, 1) != '/' && $route = '/' . $route;

        // 判断是否在模块内容
        if (true === Yii::$app->params['inAddon']) {
            $route = StringHelper::replace('/addons/', '', $route);
        }

        return Auth::verify($route);
    }
    
    /**
     * 多语言表单input的name值生成
     * @param unknown $lang
     * @param unknown $field
     * @return string
     */
    public static function langInputName ($model,$language,$field)
    {
        $className = substr(strrchr($model->className(), '\\'), 1);
        return "{$className}[{$language}][{$field}]";
    }
    /**
     * 多语言input参数
     * @param unknown $model
     * @param unknown $language
     * @param unknown $field
     * @param array $attrKeys
     * @param array $options
     * @return array
     */
    public static function langInputOptions($model, $language, $field, $options = [])
    {
        $options['name'] = self::langInputName($model, $language, $field);
        $options['id'] = $field."_".$language;
        return $options;
    }
    
    /**
     * 多语言tab 标签初始化
     * @param array $options
     * @param string $tab
     */
    public static function langTab($tab = 'tab',$title = null)
    {
        return self::tab(Yii::$app->params['languages'],Yii::$app->language,$tab,$title);
    }
    /**
     * 地区tab 标签初始化
     * @param array $options
     * @param string $tab
     */
   /*  public static function areaTab($tab = 'areaTab',$title = null)
    {
        return self::tab(AreaEnum::getMap(),Yii::$app->params['areaId'],$tab,$title);
    } */
    /**
     * tab 标签初始化
     * @param array $options
     * @param string $tab
     */
    public static function tab($options,$curValue,$tab = 'tab',$title = null)
    {
        $str = '<ul class="nav nav-tabs">';
        if($title){
            $str .= '<li><a href="javascript:void(0)">'.$title.'</a></li>';
        }
        foreach ($options as $key=>$name){
            $active = $curValue == $key?"active":"";
            $id = $tab.'_'.$key;
            $str.='<li class="'.$active.'"><a href="#'.$id.'" id="a_'.$id.'" data-toggle="tab" aria-expanded="false">'.$name.'</a></li>';
            if($key === 0){
                $str .='<script type="text/javascript">';
                $str .= '$("#a_'.$id.'").click(function(){';
                foreach ($options as $k=>$v){
                    if($k > 0){
                        $str .='$("#'.$tab.'_'.$k.'").removeClass("active").addClass("active");';
                    }
                }
                $str .= '})</script>';
            }
        }
        $str .='</ul>';
        return $str;
    }
    /**
     * 动态导航页签
     * @param array $options
     * @param string $value 当前标签ID
     * @return string
     */
    public static function menuTab($options,$value)
    {
        $str = '<ul class="nav nav-tabs">';
        foreach ($options as $key=>$option){
            $active = $value == $key?"active":"";
            $str.='<li class="'.$active.'"><a href="'.$option['url'].'" >'.$option['name'].'</a></li>';            
        }
        $str .='</ul>';
        return $str;
    }
    
    /**
     * 批量审核
     * @param array|string $url
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function batchAudit($url = ['ajax-batch-audit'], $content = '审核', $options = [])
    {
        $options = ArrayHelper::merge([
                'class' => "btn btn-primary btn-sm",
                'onclick' => "batchAudit(this);return false;"
        ], $options);
        
        return self::a($content, $url, $options);
    }

    /**
     * 批量弹框操作
     * @param array|string $url
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function batchPop($url = [], $content = '批量弹框', $options = [])
    {
        $options = ArrayHelper::merge([
            'class' => "btn btn-primary btn-sm",
            'onclick' => "batchPop(this);return false;"
        ], $options);

        return self::a($content, $url, $options);
    }
    
    /**
     * 批量操作按钮
     * @param array $options
     * @return string
     */
    public static function batchButtons($options = [])
    {
        if($options === false) return '';
        
        $listBut = [
                'status_enabled' => self::tag('span', '批量启用',
                        [
                                'class' => "btn btn-success btn-sm jsBatchStatus",
                                "data-grid"=>"grid",
                                "data-value"=>"1",
                        ]),
                'status_disabled' => self::tag('span', '批量禁用',
                        [
                                'class' => "btn btn-default btn-sm jsBatchStatus",
                                "data-grid"=>"grid",
                                "data-value"=>"0",
                        ]),
                 'search_export' => self::tag('span', '批量导出',
                         [
                         'class' => "btn btn-primary btn-sm jsBatchExport",
                         "data-grid"=>"grid",
                         ]), 
                /* 'status_delete' => self::tag('span', '批量删除',
                 [
                 'class' => "btn btn-danger btn-sm jsBatchStatus",
                 "data-grid"=>"grid",
                 "data-value"=>"-1",
                 ]), */
                'batch_delete' => self::tag('span', '批量删除',
                        [
                                'class' => "btn btn-danger btn-sm jsBatchStatus",
                                "data-grid"=>"grid",
                                "data-url"=>Url::to(['ajax-batch-delete']),
                        ]),
        ];
        $buttonHtml = "";
        if(is_array($options) && !empty($options)){
            foreach ($options as $key=>$val){
                if(isset($listBut[$val]) && (is_numeric($key) || empty($val))){
                    $buttonHtml .= $listBut[$val].'  ';
                }else{
                    $buttonHtml .= $val.'  ';
                }
            }
        }else{
            foreach ($listBut as $key=>$val){
                $buttonHtml .= $val.'  ';
            }
        }
        return $buttonHtml;
    }
}