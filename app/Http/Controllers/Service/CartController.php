<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\YzResult;
use Illuminate\Http\Request;
use App\Entity\CartItem;

class CartController extends Controller {

    public function addCart(Request $request, $product_id) {

        $yz_result = new YzResult;
        $yz_result->status = 0;
        $yz_result->message = '添加成功';

        // 如果当前已经登录
        $member = $request->session()->get('member', '');
        if ($member != '') {
            $cart_items = CartItem::where('member_id', $member->id)->get();

            $exist = false;
            foreach ($cart_items as $cart_item) {
                if ($cart_item->product_id == $product_id) {
                    $cart_item->count ++;
                    $cart_item->save();
                    $exist = true;
                    break;
                }
            }

            if ($exist == false) {
                $cart_item = new CartItem;
                $cart_item->product_id = $product_id;
                $cart_item->count = 1;
                $cart_item->member_id = $member->id;
                $cart_item->save();
            }

            return $yz_result->toJson();
        }

        // 未登录,则从cookie中获取购物车的值
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

        return response($yz_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
    }

    public function deleteCart(Request $request) {
        $yz_result = new YzResult;
        $yz_result->status = 0;
        $yz_result->message = '删除成功';

        $product_ids = $request->input('product_ids', '');
        if ($product_ids == '') {
            $yz_result->status = 1;
            $yz_result->message = '书籍ID为空';
            return $yz_result->toJson();
        }
        $product_ids_arr = explode(',', $product_ids);

        $member = $request->session()->get('member', '');
        if ($member != '') {
            // 已登录
            CartItem::whereIn('product_id', $product_ids_arr)->delete();
            return $yz_result->toJson();
        }

        $product_ids = $request->input('product_ids', '');
        if ($product_ids == '') {
            $yz_result->status = 1;
            $yz_result->message = '书籍ID为空';
            return $yz_result->toJson();
        }

        // 未登录
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
        foreach ($bk_cart_arr as $key => $value) {
            $index = strpos($value, ':');
            $product_id = substr($value, 0, $index);
            // 存在, 删除
            if (in_array($product_id, $product_ids_arr)) {
                array_splice($bk_cart_arr, $key, 1);
                continue;
            }
        }

        return response($yz_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
    }

}
