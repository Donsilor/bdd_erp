<?php

namespace addons\Style\common\forms;

use Yii;
use yii\base\Model;
use addons\Style\common\models\Style;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 款式搜索 searchForm
 *
 */
class StyleSearchForm extends Model
{
    public $style_sn;
    public $style_name;
    public $style_sex;
    public $style_material;
    public $audit_status;
    public $style_cate_id;
    public $product_type_id;
    public $style_source_id;
    public $style_channel_id;
    public $is_made;
    public $is_inlay;
    public $is_gift;
    public $remark;
    public $status;
    public $creator_id;
    public $created_at;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_sn', 'style_name', 'remark', 'created_at'], 'string'],
            [['style_sex', 'style_material', 'audit_status', 'style_cate_id', 'product_type_id', 'style_source_id', 'style_channel_id', 'is_made', 'is_inlay', 'is_gift', 'status', 'creator_id'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function style_sns()
    {
        $styleArr = StringHelper::explodeIds($this->style_sn);
        return $styleArr;
    }

    /**
     * @return array
     */
    public function style_name()
    {
        return trim($this->style_name);
    }

    /**
     * @return array
     */
    public function style_remark()
    {
        return trim($this->remark);
    }

    /**
     * 分类id
     *
     * @return array
     */
    public function styleCateIds()
    {
        $style_cate_id_arr = [];
        $style_cate_id = $this->style_cate_id;
        if (is_array($style_cate_id)) {
            foreach ($style_cate_id as $cate_id) {
                $style_cate_id_arr = ArrayHelper::merge($style_cate_id_arr, \Yii::$app->styleService->styleCate->findChildIdsById($cate_id));
            }
        } else {
            $style_cate_id_arr = ArrayHelper::merge($style_cate_id_arr, \Yii::$app->styleService->styleCate->findChildIdsById($style_cate_id));
        }
        return $style_cate_id_arr;
    }


    /**
     * 产品id
     *
     * @return array
     */
    public function proTypeIds()
    {
        $product_type_id_arr = [];
        $product_type_id = $this->product_type_id;
        if (is_array($product_type_id)) {
            foreach ($product_type_id as $pro_type_id) {
                $product_type_id_arr = ArrayHelper::merge($product_type_id_arr, \Yii::$app->styleService->productType->findChildIdsById($pro_type_id));
            }
        } else {
            $product_type_id_arr = ArrayHelper::merge($product_type_id_arr, \Yii::$app->styleService->productType->findChildIdsById($product_type_id));
        }
        return $product_type_id_arr;
    }

    /**
     * 创建时间
     *
     * @return array
     */
    public function betweenCreatedAt()
    {
        if ($this->created_at
            && count($created_ats = explode('/', $this->created_at)) == 2) {
            $created_at_start = strtotime($created_ats[0]);
            $created_at_end = strtotime($created_ats[1]) + 86400;
            if (!empty($created_at_start) && !empty($created_at_end)) {
                return ['between', Style::tableName() . '.created_at', $created_at_start, $created_at_end];
            }
            if (!empty($created_at_start)) {
                return ['>=', Style::tableName() . '.created_at', $created_at_start];
            }
            if (!empty($created_at_end)) {
                return ['<=', Style::tableName() . '.created_at', $created_at_end];
            }
        };
        return [];
    }
}
