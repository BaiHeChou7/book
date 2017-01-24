<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YzResult;

class MemberController extends Controller
{
    public function register(Request $request){
        $email = $request->input('email', '');
        $phone = $request->input('phone', '');
        $password = $request->input('password', '');
        $confirm = $request->input('confirm', '');
        $phone_code = $request->input('phone_code', '');
        $validate_code = $request->input('validate_code', '');
        
        
        // 前后端都要进行验证
        $yz_result = new YzResult;

        if($email == '' && $phone == '') {
          $yz_result->status = 1;
          $yz_result->message = '手机号或邮箱不能为空';
          return $yz_result->toJson();
        }
        if($password == '' || strlen($password) < 6) {
          $yz_result->status = 2;
          $yz_result->message = '密码不少于6位';
          return $yz_result->toJson();
        }
        if($confirm == '' || strlen($confirm) < 6) {
          $yz_result->status = 3;
          $yz_result->message = '确认密码不少于6位';
          return $yz_result->toJson();
        }
        if($password != $confirm) {
          $yz_result->status = 4;
          $yz_result->message = '两次密码不相同';
          return $yz_result->toJson();
        }
        
        
    }
}
