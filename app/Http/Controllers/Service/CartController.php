<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\YzResult;
use Illuminate\Http\Request;

class CartController extends Controller {

    public function addCart(Request $request, $product_id) {
        // 从cookie中获取购物车的值
        $bk_cart = $request->cookie('bk_cart');
//        return $bk_cart;
        // 如果内容不为空,拆分字符串
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
        $count = 1;
        // cart数组是基本类型的数组,不是对象数组. 所以对value的操作只会传值.并没有修改数组中的引用. 所以 这里必须传引用 
        foreach ($bk_cart_arr as &$value) {
            $index = strpos($value, ':');
            if (substr($value, 0, $index) == $product_id) {
                // 如果商品已存在, 在原有的基础上+1
                $count = ((int) substr($value, $index + 1)) + 1;
                $value = $product_id . ':' . $count;
                break;
            }
        }
        // 如果商品不存在,将商品id psuh进数组
        if ($count == 1) {
            array_push($bk_cart_arr, $product_id . ':' . $count);
        }
        $yz_result = new YzResult;
        $yz_result->status = 0;
        $yz_result->message = '添加成功';

        return response($yz_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
    }

    public function deleteCart(Request $request) {
        $yz_result = new YzResult;

        $request_ids = $request->input('product_ids', '');
        if ($request_ids == '') {
            $yz_result->status = 1;
            $yz_result->message = '书籍id为空';
            return $yz_result;
        }

        $request_ids_arr = explode(',', $request_ids);
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
        foreach ($bk_cart_arr as $key => $value) {
            $index = strpos($value, ':');
            $product_id = substr($value, 0, $index);
            // 判断产品id是否存在购物车,存在即删除
            if (in_array($product_id, $request_ids_arr)) {
                array_splice($bk_cart_arr, $key, 1);
                continue;
            }
        }
        $yz_result->status = 0;
        $yz_result->message = '删除成功';
        return response($yz_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
    }

}
