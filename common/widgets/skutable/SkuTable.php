<?php

namespace common\widgets\skutable;

use yii\base\Widget;
use common\widgets\skutable\assets\AppAsset;

/**
 * Class Provinces
 * @package common\widgets\provinces
 * @author jianyan74 <751393839@qq.com>
 */
class SkuTable extends Widget
{
    /**
     * SKU数据
     *'data' =>[
            [
                    'id'=>1,
                    'name'=>'颜色',
                    'value'=>[
                             1=>'16G',
                             2=>'32G',                                                 
                             3=>'64G',                                              
                             4=>'128G',
                    ],
                    'current'=>[1,3,4]
            ],
            [
                    'id'=>2,
                    'name'=>'净度',
                    'value'=>[
                            11=>'SI',
                            12=>'V'
                     ],
                    'current'=>[11]
            ]
            
    ],
     * @var
     */
    public $data;


    /**
     * 模板
     *
     * long/short
     *
     * @var string
     */
    public $template = 'default';

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
    public $name = 'StyleGoods';
    public $inputs;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $currency = \Yii::$app->params['currency'];
        $asset = AppAsset::register($this->getView());
        if(!isset($this->inputs)){
            $this->inputs =  [                 
                    ['name'=>'status','title'=>'操作','require'=>0,'batch'=>0,'unique'=>0,'dtype'=>"integer"],
                    ['name'=>'goods_sn','title'=>"商品编号",'require'=>0,'batch'=>0,'unique'=>0,'dtype'=>"string",'style'=>'width:150px','attrs'=>'disabled placeholder=\'系统自动生成\''],
                    ['name'=>'cost_price','title'=>"成本价({$currency})",'require'=>1,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:100px'],
                    ['name'=>'second_stone_weight1','title'=>'副石1重量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                    ['name'=>'second_stone_num1','title'=>'副石1数量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:50px'],
                    ['name'=>'second_stone_weight2','title'=>'副石2重量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                    ['name'=>'second_stone_num2','title'=>'副石2数量','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:50px'],
                    ['name'=>'g18k_weight','title'=>'18K标准金重','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                    ['name'=>'g18k_diff','title'=>'18K上下公差','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                    ['name'=>'pt950_weight','title'=>'PT950标准金重','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:80px'],
                    ['name'=>'pt950_diff','title'=>'PT950上下公差','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:80px'],
                    ['name'=>'silver_weight','title'=>'银标准金重','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                    ['name'=>'silver_diff','title'=>'银上下公差','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:70px'],
                    ['name'=>'finger_range','title'=>'改圈范围','require'=>0,'batch'=>1,'unique'=>0,'dtype'=>"double",'style'=>'width:50px'],
                    
            ];            
        }        
    }

    /**
     * @return string
     */
    public function run()
    {
        
        return $this->render($this->template, [
            'form' => $this->form,
            'model' => $this->model,
            'name' => $this->name,   
            'data' => $this->data,
            'inputs'=>$this->inputs,
        ]);
    }
}

?>