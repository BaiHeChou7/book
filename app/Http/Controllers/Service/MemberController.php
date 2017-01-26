<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YzResult;
use App\Entity\Member;
use App\Entity\TempPhone;
use App\Entity\TempEmail;
use App\Models\YzEmail;
use App\Tool\UUID;
use Mail;

class MemberController extends Controller {

    public function register(Request $request) {
        $email = $request->input('email', '');
        $phone = $request->input('phone', '');
        $password = $request->input('password', '');
        $confirm = $request->input('confirm', '');
        $phone_code = $request->input('phone_code', '');
        $validate_code = $request->input('validate_code', '');

        $yz_result = new YzResult;

        if ($email == '' && $phone == '') {
            $yz_result->status = 1;
            $yz_result->message = '手机号或邮箱不能为空';
            return $yz_result->toJson();
        }
        if ($password == '' || strlen($password) < 6) {
            $yz_result->status = 2;
            $yz_result->message = '密码不少于6位';
            return $yz_result->toJson();
        }
        if ($confirm == '' || strlen($confirm) < 6) {
            $yz_result->status = 3;
            $yz_result->message = '确认密码不少于6位';
            return $yz_result->toJson();
        }
        if ($password != $confirm) {
            $yz_result->status = 4;
            $yz_result->message = '两次密码不相同';
            return $yz_result->toJson();
        }

        // 手机号注册
        if ($phone != '') {
            if ($phone_code == '' || strlen($phone_code) != 6) {
                $yz_result->status = 5;
                $yz_result->message = '手机验证码为6位';
                return $yz_result->toJson();
            }

            $tempPhone = TempPhone::where('phone', $phone)->first();
            if ($tempPhone->code == $phone_code) {
                if (time() > strtotime($tempPhone->deadline)) {
                    $yz_result->status = 7;
                    $yz_result->message = '手机验证码不正确';
                    return $yz_result->toJson();
                }

                $member = new Member;
                $member->phone = $phone;
                $member->password = md5('yz' + $password);
                $member->save();

                $yz_result->status = 0;
                $yz_result->message = '注册成功';
                return $yz_result->toJson();
            } else {
                $yz_result->status = 7;
                $yz_result->message = '手机验证码不正确';
                return $yz_result->toJson();
            }

            // 邮箱注册
        } else {
            if ($validate_code == '' || strlen($validate_code) != 4) {
                $yz_result->status = 6;
                $yz_result->message = '验证码为4位';
                return $yz_result->toJson();
            }

            $validate_code_session = $request->session()->get('validate_code', '');
            if ($validate_code_session != $validate_code) {
                $yz_result->status = 8;
                $yz_result->message = '验证码不正确';
                return $yz_result->toJson();
            }

            $member = new Member;
            $member->email = $email;
            $member->password = md5('yz' + $password);
            $member->save();

            $uuid = UUID::create();

            $yz_email = new YzEmail;
            $yz_email->to = $email;
            $yz_email->cc = 'yuzhou_shop@163.com';
            $yz_email->subject = '余洲书店验证';
            $yz_email->content = '请于24小时点击该链接完成验证. http://www.yzbook.com/service/validate_email'
                    . '?member_id=' . $member->id
                    . '&code=' . $uuid;

            $tempEmail = new TempEmail;
            $tempEmail->member_id = $member->id;
            $tempEmail->code = $uuid;
            $tempEmail->deadline = date('Y-m-d H-i-s', time() + 24 * 60 * 60);
            $tempEmail->save();

            Mail::send('email_register', ['yz_email' => $yz_email], function ($m) use ($yz_email) {
                // $m->from('hello@app.com', 'Your Application');
                $m->to($yz_email->to, '尊敬的用户')
                        ->cc($yz_email->cc)
                        ->subject($yz_email->subject);
            });

            $yz_result->status = 0;
            $yz_result->message = '注册成功';
            return $yz_result->toJson();
        }
    }

    // 用户登录
    public function login(Request $request) {
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        $validate_code = $request->get('validate_code', '');

        $yz_result = new YzResult;

        // 校验
        // 判断
        // 判断验证码是否正确
        $validate_code_session = $request->session()->get('validate_code');
        if ($validate_code != $validate_code_session) {
            $yz_result->status = 1;
            $yz_result->message = '验证码不正确';
            return $yz_result->toJson();
        }
        $member = null;
        // 查询
        if (strpos($username, '@') == true) {
            $member = Member::where('email', $username)->first();
        } else {
            $member = Member::where('phone', $username)->first();
        }

        if ($member == null) {
            $yz_result->status = 2;
            $yz_result->message = '该用户不存在';
            return $yz_result->toJson();
        } else {
            if (md5('yz' + $password) != $member->password) {
                $yz_result->status = 3;
                $yz_result->message = '密码不正确';
                return $yz_result->toJson();
            }
        }
        $request->session()->put('memeber',$member);
        $yz_result->status = 0;
        $yz_result->message = '登录成功';
        return $yz_result->toJson();
    }

}
