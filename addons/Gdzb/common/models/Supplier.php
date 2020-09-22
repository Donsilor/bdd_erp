<?php

namespace addons\Gdzb\common\models;

use addons\Sales\common\models\SaleChannel;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "gdzb_supplier".
 *
 * @property int $id 供应商ID
 * @property int $merchant_id 商户ID
 * @property string $supplier_code 供应商编码
 * @property string $supplier_name 供应商名称
 * @property string $business_scope 经营范围(逗号隔开的id)
 * @property string $bank_name 开户行
 * @property string $bank_account 银行账户
 * @property string $bank_account_name 开户姓名
 * @property string $contactor 供应商联系人
 * @property string $telephone 供应商联系电话
 * @property string $mobile 供应商联系人手机
 * @property string $address 供应商地址(取货地址)
 * @property string $bdd_contactor BDD紧急联系人
 * @property string $bdd_mobile BDD紧急联系人手机
 * @property string $bdd_telephone BDD紧急联系人电话
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 供应商备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $follower_id 归属客服
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Supplier extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('supplier');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'follower_id', 'trade_num','type','level','creator_id','channel_id', 'created_at', 'updated_at'], 'integer'],
            [['contactor','wechat','channel_id','source_id'], 'required'],
            [['supplier_code', 'bank_account', 'bank_account_name', 'contactor', 'telephone', 'mobile', 'bdd_contactor', 'bdd_mobile', 'bdd_telephone','wechat'], 'string', 'max' => 30],
            [['supplier_name', 'address'], 'string', 'max' => 120],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
            [['bank_name'], 'string', 'max' => 100],
            [['business_scope'], 'safe'],
            [['wechat'], 'unique'],
            [['business_scope'], 'parseBusinessScope'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '供应商ID',
            'merchant_id' => '商户ID',
            'supplier_code' => '供应商编码',
            'supplier_name' => '供应商档口',
            'business_scope' => '经营范围',
            'bank_name' => '开户行',
            'bank_account' => '银行账户',
            'bank_account_name' => '开户姓名',
            'contactor' => '姓名',
            'telephone' => '电话',
            'wechat' => '微信',
            'mobile' => '手机号',
            'address' => '供应商地址(取货地址)',
            'bdd_contactor' => 'BDD紧急联系人',
            'bdd_mobile' => 'BDD紧急联系人手机',
            'bdd_telephone' => 'BDD紧急联系人电话',
            'channel_id' => '所属渠道',
            'source_id' => '供应商来源',
            'trade_num' => '交易单量',
            'type' => '类型',
            'level' => '等级',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '备注',
            'sort' => '排序',
            'status' => '状态',
            'follower_id' => '归属客服',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 对应快递模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('saleChannel');
    }


    /**
     * 经营范围
     */
    public function parseBusinessScope()
    {
        if(is_array($this->business_scope)){
            $this->business_scope = ','.implode(',',$this->business_scope).',';
        }
        return $this->business_scope;
    }

    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
    /**
     * 对应跟进人（管理员）模型
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(Member::class, ['id'=>'follower_id'])->alias('follower');
    }
}
