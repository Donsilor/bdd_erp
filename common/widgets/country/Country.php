<?php

namespace common\widgets\country;

use Yii;
use yii\base\Widget;

/**
 * Class country
 * @package common\widgets\country
 * @author jianyan74 <751393839@qq.com>
 */
class Country extends Widget
{
    /**
     * 国家字段名
     *
     * @var
     */
    public $countryName = 'country';

    /**
     * 省份字段名
     *
     * @var
     */
    public $provinceName = 'province';

    /**
     * 城市字段名
     *
     * @var
     */
    public $cityName = 'city';

    /**
     * 显示类型
     *
     * long/short
     *
     * @var string
     */
    public $template = 'short';

    /**
     * 关联的ajax url
     *
     * @var
     */
    public $url;

    /**
     * 级别
     *
     * @var int
     */
    public $level = 3;

    /**
     * 模型
     *
     * @var array
     */
    public $model;

    /**
     * 表单
     * @var
     */
    public $form;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        empty($this->url) && $this->url = Yii::$app->urlManager->createUrl(['/country/index']);
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->template, [
            'form' => $this->form,
            'model' => $this->model,
            'countryName' => $this->countryName,
            'provinceName' => $this->provinceName,
            'cityName' => $this->cityName,
            'url' => $this->url,
            'level' => $this->level,
        ]);
    }
}

?>