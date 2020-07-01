<?php

namespace common\widgets\webuploader;

use function GuzzleHttp\Psr7\str;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use common\helpers\StringHelper;
use common\components\UploadDrive;
use common\models\common\Attachment;
use common\widgets\webuploader\assets\AppAsset;

/**
 * 图片上传
 *
 * Class Images
 * @package common\widgets\webuploader
 * @author jianyan74 <751393839@qq.com>
 */
class MultipleFiles extends InputWidget
{
    /**
     * webuploader参数配置
     *
     * @var array
     */
    public $config = [];

    /**
     * @var string
     */
    public $type = 'images';

    /**
     * 默认主题
     *
     * @var string
     */
    public $theme = 'multiple-default';

    /**
     * 默认主题配置
     *
     * @var array
     */
    public $themeConfig = [];

    /**
     * 盒子ID
     *
     * @var
     */
    protected $boxId;

    /**
     * @var
     */
    protected $typeConfig;

    /**
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $uploadUrl = [
            'images' => Url::to(['/file/images']),
            'videos' => Url::to(['/file/videos']),
            'voices' => Url::to(['/file/voices']),
            'files' => Url::to(['/file/files']),
        ];

        // 默认配置信息
        $this->typeConfig = Yii::$app->params['uploadConfig'][$this->type];
        $this->boxId = md5($this->name) . StringHelper::uuid();

        $this->themeConfig = ArrayHelper::merge([
            'select' => true, // 显示选择文件
            'sortable' => true, // 是否开启排序
        ], $this->themeConfig);

        $this->config = ArrayHelper::merge([
            'compress' => false, // 压缩
            'auto' => false, // 自动上传
            'formData' => [
                'guid' => null,
                'md5' => null,
                'writeTable' => true,
                'drive' => $this->typeConfig['drive'], // 默认本地 可修改 qiniu/oss/cos 上传
            ], // 表单参数
            'pick' => [
                'id' => '.upload-album-' . $this->boxId,
                'innerHTML' => '',// 指定按钮文字。不指定时优先从指定的容器中看是否自带文字。
                'multiple' => false, // 是否开起同时选择多个文件能力
            ],
            'accept' => [
                'title' => 'Images',// 文字描述
                'extensions' => implode(',', $this->typeConfig['extensions']), // 后缀
                'mimeTypes' => $this->typeConfig['mimeTypes'],// 上传文件类型
            ],
            'swf' => null, //
            'chunked' => false,// 开启分片上传
            'chunkSize' => 10 * 1024 * 1024,// 分片大小
            'server' => $uploadUrl[$this->type], // 默认上传地址
            'fileVal' => 'file', // 设置文件上传域的name
            'disableGlobalDnd' => true, // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            'fileNumLimit' => 20, // 验证文件总数量, 超出则不允许加入队列
            'fileSizeLimit' => null, // 验证文件总大小是否超出限制, 超出则不允许加入队列 KB
            'fileSingleSizeLimit' => $this->typeConfig['maxSize'], // 验证单个文件大小是否超出限制, 超出则不允许加入队列 KB
            'prepareNextFile' => true,
            'duplicate' => true,

            /**-------------- 自定义的参数 ----------------**/
            'independentUrl' => false, // 独立上传地址,不受全局的地址上传影响
            'mergeUrl' => Url::to(['/file/merge']),
            'getOssPathUrl' => Url::to(['/file/get-oss-path']),
            'verifyMd5Url' => Url::to(['/file/verify-md5']),
            'callback' => null, // 上传成功回调js方法
            'callbackProgress' => null, // 上传进度回调
            'name' => $this->name,
            'boxId' => $this->boxId,
            'type' => $this->type,
        ], $this->config);

        if (!empty($this->typeConfig['takeOverUrl']) && $this->config['independentUrl'] == false) {
            $this->config['server'] = $this->typeConfig['takeOverUrl'];
        }        
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {        
        $value = $this->hasModel() ? Html::getAttributeValue($this->model, $this->attribute) : $this->value;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
        $boxId = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->boxId;

        empty($value) && $value = [];
        if ($this->config['pick']['multiple'] == true ) {
            // 赋予默认值
            $name = $name . '[]';

            try {
                if ($value && !is_array($value)) {
                    $value = json_decode($value, true);
                    empty($value) && $value = unserialize($value);
                    empty($value) && $value = [];
                }
            } catch (\Exception $e) {
                $value = [];
            }
        }

        if (!is_array($value)) {
            $tmp = $value;
            $value = [];
            $value[] = $tmp;
        }

        //  由于百度上传不能传递数组，所以转码成为json
        !isset($this->config['formData']) && $this->config['formData'] = [];

        // 阿里云直传
        if (Attachment::DRIVE_OSS_DIRECT_PASSING == $this->config['formData']['drive']) {
            $path = $this->typeConfig['path'] . date($this->typeConfig['subName'], time()) . "/";
            $oss = Yii::$app->uploadDrive->oss()->config($this->config['fileSingleSizeLimit'], $path, 60 * 60 * 2, $this->type);
            $this->config['server'] = $oss['host'];
            $this->config['formData'] = ArrayHelper::merge($this->config['formData'] , $oss);
        }

        foreach ($this->config['formData'] as &$datum) {
            if (!empty($datum) && is_array($datum)) {
                $datum = Json::encode($datum);
            }
        }
        $this->registerClientScript($name,$boxId);
        return $this->render($this->theme, [
            'name' => $name,
            'value' => $value,
            'type' => $this->type,
            'boxId' => $boxId,
            'config' => $this->config,
            'themeConfig' => $this->themeConfig,
        ]);
    }

    /**
     * 注册资源
     */
    protected function registerClientScript($name,$boxId)
    {
        $view = $this->getView();
        AppAsset::register($view);
//        $boxId = $this->boxId;
        $jsConfig = Json::encode($this->config);
        $disabled = $this->themeConfig['sortable'] ?? true;

        $view->registerJs(<<<Js
    var uploadObj,uploadTrigers;
    $(document).on("click", ".selectMuti",function(){
        imageObj = $(this);
    });
    var sortable = '{$disabled}';
    if (sortable) {
           // 拖动排序
        Sortable.create(document.getElementById('{$boxId}'),{
            distance : 30,
            filter : ".upload-box"
        }); 
    }
        
    $(".upload-album-{$boxId}").InitMultiUploader({$jsConfig});
    // 兼容老IE
    document.body.ondrop = function (event) {
        event = event || window.event;
        if (event.preventDefault) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.returnValue = false;
            event.cancelBubble = true;
        }
    };
    
    
    
    var boxId = "{$boxId}";
    var closeFiles = {};
    var iss = null;
    var isss = null;
    // 删除图片节点
    $(document).on("click", ".delimg", function() {
        let parentObj = $(this).parent().parent();
        let multiple =  $(this).data('multiple');
        let name = parentObj.parent().attr('data-name');
        let boxId = parentObj.parent().attr('data-boxId');

        if (multiple == true) {
            name = name.substring(0, name.length - 2);
        }

        let input = '<input type="hidden" name="' + name + '" value="" id="hideInput-' + boxId + '">';

        // 判断是否是多图上传
        if (multiple === '' || multiple === false) {
            //增加值为空的隐藏域
            parentObj.parent().append(input);
            //显示上传图片按钮
            parentObj.next("li").removeClass('hide');
        } else {
            // 增加值为空的隐藏域
            let length = parentObj.parent().find('li').length;
            if (length === 2) {
                parentObj.parent().append(input);
            }
        }

        parentObj.remove();
    });
    $(".upload-box").click(function(){
          uploadObj = $(this);
          uploadTrigers  = 0;
    });
    // 上传成功
    $(document).on('upload-success-' + boxId, function(e, data, config){
        if (uploadTrigers > 0) {
            return;
        }
        uploadTrigers++;
        var name = uploadObj.parent().attr('data-name');
        
        let multiple = config.pick.multiple;
        // 判断是否是多图上传
        let obj = uploadObj;
        if (multiple === 'false' || multiple === false){
            $(obj).addClass('hide');
        }
        var arr = data.url.split('.');

        // 增加显示
        let callData = [];
        callData["id"] = data.id;
        callData["value"] = data.url;
        callData["extend"] = '.' + arr[arr.length - 1];
        callData["upload_type"] = data.upload_type;
        callData["multiple"] = multiple;
        let html = getTemplate(callData,name);

        // 查找文本框并移除
        $(obj).parent().find('#hideInput-' + boxId).remove();
        $(obj).before(html);
    });

    // 上传失败
    $(document).on('upload-error-' + boxId, function(e, file, reason, uploader, config){
        uploader.removeFile(file); //从队列中移除
        rfError("上传失败，服务器错误");
    });

    // 文件添加进来的时候
    $(document).on('upload-file-queued-' + boxId, function(e, file, uploader, config){
        let parentObj = getParent(config);
    });

    // 一批文件添加进来的时候
    $(document).on('upload-files-queued-' + boxId, function(e, files, uploader, config){
        let parentObj = getParent(config);
    });

    // 上传不管成功还是失败回调
    $(document).on('upload-complete-' + boxId, function(e, file, num, config, uploadProgress){
        let parentObj = getParent(config);
        var remove = true;
        // 如果队列为空，则移除进度条
        jQuery.each(uploadProgress, function(i, val) {
            var tmpVal = parseInt(val);
            if (tmpVal >= -1 && tmpVal < 100 && closeFiles[i] === undefined) {
                remove = false;
            }
        });

        console.log(closeFiles);
        console.log(uploadProgress);

        if (remove === true) {
            parentObj.find(".upload-progress").parent().addClass('hide');
        }
    });

    // 创建进度条
    $(document).on('upload-create-progress-' + boxId, function(e, file, uploader, config){
        let parentObj = getParent(config);
        if (parentObj.children(".upload-progress").hasClass('hide')) {
            parentObj.children(".badge").html("0%");
            let progressCancel = parentObj.find('.cancel');
            //绑定点击事件
            progressCancel.click(function() {
                uploader.cancelFile(file);
                closeFiles[file.id] = true;
                parentObj.find('.upload-progress').parent().addClass('hide');
            });

            parentObj.find('.upload-progress').parent().removeClass('hide');
        }
    });

    // 实时进度条
    $(document).on('upload-progress-' + boxId, function(e, file, percentage, config){
        let parentObj = getParent(config);
        let progressObj = parentObj.find(".upload-progress");
        percentage = Math.floor(percentage * 100);

        if (percentage > 1) {
            percentage -= 1;
        }

        progressObj.find(".badge").attr('percentage', percentage);
        progressObj.find(".badge").html(percentage + "%");
    });

    // md5创建验证中
    $(document).on('md5Verify-create-progress-' + boxId, function(e, file, uploader, config, text = "验证中..."){
        let parentObj = getParent(config);
        if (parentObj.children(".upload-progress").length === 0) {
            parentObj.find(".badge").html(text);
            let progressCancel = parentObj.find('.cancel');
            //绑定点击事件
            progressCancel.click(function() {
                uploader.cancelFile(file);
                parentObj.find('.upload-progress').parent().addClass('hide');
            });

            parentObj.find('.upload-progress').parent().removeClass('hide');
        }
    });
    // 选择回调
    $(document).on('select-file-' + boxId, function(e, boxId, data){
       
        if (uploadTrigers > 0 || data.length === 0) {
            return;
        }
        uploadTrigers++;
        let multiple =  $('#' + boxId).data('multiple');
        // 判断是否是多图上传
        let obj = uploadObj;
        var name = uploadObj.parent().attr('data-name');
        
        if (multiple === 'false' || multiple === false || multiple === ''){
            $(obj).addClass('hide');
            // 增加显示
            var arr = data[0].url.split('.');
            let callData = [];
            callData["id"] = data[0].id;
            callData["value"] = data[0].url;
            callData["upload_type"] = data[0].upload_type;
            callData["extend"] = '.' + arr[arr.length - 1];
            callData["multiple"] = multiple;
            let html = getTemplate(callData,name);
            $(obj).before(html);
        } else {
            for (let i = 0; i < data.length; i++) {
                // 增加显示
                var arr = data[i].url.split('.');
                let callData = [];
                callData["id"] = data[i].id;
                callData["value"] = data[i].url;
                callData["upload_type"] = data[i].upload_type;
                callData["extend"] = '.' + arr[arr.length - 1];
                callData["multiple"] = multiple;
                let html = getTemplate(callData,name);
                $(obj).before(html);
            }
        }

        // 查找文本框并移除
        $(obj).parent().find('#hideInput-' + boxId).remove();
    });

    // 获取当前的父类
    function getParent(config) {
        let boxId = config.boxId;
        return $('#' + boxId);
    }
    
    
   function getTemplate(callData , name) {
       var template = '<li><input type="hidden" name="'+ name +'" value="'+ callData['value'] +'"><div class="img-box">';
           if(callData["upload_type"] == 'images'){
               template += '<a href="' + callData['value'] + '" data-fancybox="rfUploadImg"><div class="bg-cover" style="background-image: url('+ callData['value'] +');"></div></a>';
           }else{
               template +=  '<i class="fa fa-file-o"></i> <i class="upload-extend">' + callData['extend'] + '</i><div class="bottom-bar"><a href="' + callData['value'] + '" target="_blank">预览</a></div>'
           }
       template += '<i class="delimg" data-multiple="' + callData['multiple'] + '"></i></div></li>';
       return template
   }
    
    
    
Js
        );
    }
}