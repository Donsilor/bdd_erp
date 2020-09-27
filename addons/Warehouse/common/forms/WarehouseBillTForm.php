<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;

/**
 * 收货单 Form
 *
 */
class WarehouseBillTForm extends WarehouseBill
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
            [['put_in_type', 'supplier_id'], 'required'],//, 'to_warehouse_id'
            [['file'], 'file', 'extensions' => ['csv']],//'skipOnEmpty' => false,
            [['bill_no'], 'match', 'pattern' => "/^[A-Z][A-Z0-9-]*$/", 'message' => '单据编号必须大写英文字母开头，只能包含大写字母，英文横杠，数字'],
         ];
         return array_merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            //'supplier_id' => '加工商',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
            'file' => '上传货品明细',
        ]);
    }
}
