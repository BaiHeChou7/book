<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Entity\Category;
use App\Models\YzResult;

class BookController extends Controller {

    public function getCategoryByParentId($parent_id) {
        $categorys = Category::where('parent_id',$parent_id)->get();
        $yz_result = new YzResult;
        $yz_result->status = 0;
        $yz_result->message = '返回成功';
        // 脚本语言特性: js和php都是随时可以定义成员变量
        $yz_result->categorys = $categorys;
        
        return $yz_result->toJson();
    }
    
    

}
