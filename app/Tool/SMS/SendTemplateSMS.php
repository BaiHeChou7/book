<?php

namespace App\Tool\SMS;

use App\Models\YzResult;

class SendTemplateSMS
{
  //主帐号
  private $accountSid='8aaf070859b4c9930159cac1188406d0';

  //主帐号Token
  private $accountToken='a14549eefab747fca194ae8dfe1cfb69';

  //应用Id
  private $appId='8aaf070859b4c9930159cac11aea06d6';

  //请求地址，格式如下，不需要写https://
  private $serverIP='app.cloopen.com';

  //请求端口
  private $serverPort='8883';

  //REST版本号
  private $softVersion='2013-12-26';

  /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id
    */
  public function sendTemplateSMS($to,$datas,$tempId)
  {
      // 改写短信发送方法,过滤掉不需要的返回内容
       $yz_result = new YzResult;

       // 初始化REST SDK
       $rest = new CCPRestSDK($this->serverIP,$this->serverPort,$this->softVersion);
       $rest->setAccount($this->accountSid,$this->accountToken);
       $rest->setAppId($this->appId);

       // 发送模板短信
      //  echo "Sending TemplateSMS to $to <br/>";
       $result = $rest->sendTemplateSMS($to,$datas,$tempId);
       if($result == NULL ) {
           $yz_result->status = 3;
           $yz_result->message = 'result error!';
       }
       if($result->statusCode != 0) {
           $yz_result->status = $result->statusCode;
           $yz_result->message = $result->statusMsg;
       }else{
           $yz_result->status = 0;
           $yz_result->message = '发送成功';
       }

       return $yz_result;
  }
}

//sendTemplateSMS("18576437523", array(1234, 5), 1);
