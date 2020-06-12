<?php
namespace common\helpers;

use yii\data\Pagination;
use yii\db\ActiveQueryInterface;
class PageHelper {
    
    /**
     * 分页
     * @param ActiveQueryInterface $query
     * @param string $page
     * @param string $page_size
     * @param string $isArray 返回结果是否是数组列表 true是   false否
     * @return array
     */
    public static function pagination($query,$page = null,$page_size = null,$total_count = null,$returnArray = true){
        //分页默认值
        $page = $page?($page-1):0;
        $page_size = $page_size?$page_size:20;
		if(empty($total_count)) {
			$total_count = $query->count();
		}
        $pages = new Pagination(['totalCount' =>$query->count(),'page'=>$page,'pageSize'=>$page_size]);    //实例化分页类,带上参数(总条数,每页显示条数)
        if($returnArray){
            $data = $query->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        }else{
            $data = $query->offset($pages->offset)->limit($pages->limit)->all();
        }
        return [
            'page'=>$pages->getPage()+1,
            'page_size'=>$pages->getPageSize(),
            'page_count'=>$pages->getPageCount(),
            'total_count'=>$pages->totalCount,
            'data'=>$data,
        ];
    }
    
    
}