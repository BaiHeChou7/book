<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YzResult;
use App\Entity\Admin;

class IndexController extends Controller
{
  public function login(Request $request)
  {
    $username = $request->input('username', '');
    $password = $request->input('password', '');

    $yz_result = new YzResult;

    if($username == '' || $password == '') {
      $yz_result->status = 1;
      $yz_result->message = "帐号或密码不能为空!";
      return $yz_result->toJson();
    }

    $admin = Admin::where('username', $username)->where('password', md5('bk'. $password))->first();
    if(!$admin) {
      $yz_result->status = 2;
      $yz_result->message = "帐号或密码错误!";
    } else {
      $yz_result->status = 0;
      $yz_result->message = "登录成功!";

      $request->session()->put('admin', $admin);
    }

    return $yz_result->toJson();
  }

  public function toLogin()
  {
    return view('admin.login');
  }

  public function toExit(Request $request)
  {
    $request->session()->forget('admin');
    return view('admin.login');
  }

  public function toIndex(Request $request)
  {
    $admin = $request->session()->get('admin');
    return view('admin.index')->with('admin', $admin);
  }

}
