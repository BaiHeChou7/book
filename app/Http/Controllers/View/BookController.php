<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use Illuminate\Http\Request;
use Log;

class BookController extends Controller {
    // 书籍类别
    public function toCategory() {
        Log::info("进入书籍类别");
        $categorys = Category::whereNull('parent_id')->get();
        return view('category')->with('categorys',$categorys);
    }
    //书籍列表
    public function toProduct($category_id) {
        $products = Product::where('category_id',$category_id)->get();
        return view('product')->with('products',$products);
    }
    //书籍详情
    public function toPdtContent(Request $request,$product_id) {
        $product = Product::find($product_id);
        $pdt_content = PdtContent::where('product_id',$product_id)->first();
        $pdt_images = PdtImages::where('product_id', $product_id)->get();
        
        // 进入页面之前,先获取cart内容
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
        $count = 0;
        foreach ($bk_cart_arr as $value) {   
            $index = strpos($value, ':');
            if (substr($value, 0, $index) == $product_id) {
                // 如果商品已存在, 在原有的基础上+1
                $count = (int) substr($value, $index + 1);
                break;
            }
        }
        
        return view('pdt_content')->with(['product' => $product,'pdt_content' => $pdt_content,'pdt_images' => $pdt_images,'count' => $count]);
                                  
    }

}
