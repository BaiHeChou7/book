<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entity\Member;
use App\Models\YzResult;

class MemberController extends Controller
{

  public function toMember(Request $request)
  {
    $members = Member::all();
    return view('admin.member')->with('members', $members);
  }

  public function toMemberEdit(Request $request)
  {
    $id = $request->input('id', '');
    $member = Member::find($id);
    return view('admin.member_edit')->with('member', $member);
  }

  public function memberEdit(Request $request)
  {
    $member = Member::find($request->input('id', ''));

    $member->nickname = $request->input('nickname', '');
    $member->phone = $request->input('phone', '');
    $member->email = $request->input('email', '');
    $member->save();

    $yz_result = new YzResult;
    $yz_result->status = 0;
    $yz_result->message = '添加成功';

    return $yz_result->toJson();
  }
}
