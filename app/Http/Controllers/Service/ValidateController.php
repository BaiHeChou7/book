<?php

namespace App\Http\Controllers\Service;

use App\Tool\Validate\ValidateCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tool\SMS\SendTemplateSMS;
use App\Entity\TempPhone;
use App\Models\YzResult;
use App\Entity\TempEmail;
use App\Entity\Member;

class ValidateController extends Controller
{

  // Request $request 是给$request指定类型
  public function create(Request $request)
  {
    $validateCode = new ValidateCode;
    $request->session()->put('validate_code', $validateCode->getCode());
    return $validateCode->doimg();
  }

  public function sendSMS(Request $request)
  {
    $yz_result = new YzResult;

    $phone = $request->input('phone', '');
    if($phone == '') {
      $yz_result->status = 1;
      $yz_result->message = '手机号不能为空';
      return $yz_result->toJson();
    }
    if(strlen($phone) != 11 || $phone[0] != '1') {
      $yz_result->status = 2;
      $yz_result->message = '手机格式不正确';
      return $yz_result->toJson();
    }

    $sendTemplateSMS = new SendTemplateSMS;
    $code = '';
    $charset = '1234567890';
    $_len = strlen($charset) - 1;
    for ($i = 0;$i < 6;++$i) {
        $code .= $charset[mt_rand(0, $_len)];
    }
    $yz_result = $sendTemplateSMS->sendTemplateSMS($phone, array($code, 60), 1);
    if($yz_result->status == 0) {
      $tempPhone = TempPhone::where('phone', $phone)->first();
      if($tempPhone == null) {
          // 如果该手机号没有被注册过 再new
        $tempPhone = new TempPhone;
      }
      $tempPhone->phone = $phone;
      $tempPhone->code = $code;
      $tempPhone->deadline = date('Y-m-d H-i-s', time() + 60*60);
      $tempPhone->save();
    }

    return $yz_result->toJson();
  }
  // 验证邮箱
  public function validateEmail(Request $request)
  {
    $member_id = $request->input('member_id', '');
    $code = $request->input('code', '');
    if($member_id == '' || $code == '') {
      return '验证异常';
    }

    $tempEmail = TempEmail::where('member_id', $member_id)->first();
    if($tempEmail == null) {
      return '验证异常';
    }

    if($tempEmail->code == $code) {
      if(time() > strtotime($tempEmail->deadline)) {
        return '该链接已失效';
      }

      $member = Member::find($member_id);
      $member->active = 1;
      $member->save();

      return redirect('/login');
    } else {
      return '该链接已失效';
    }
  }
}
