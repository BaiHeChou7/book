<?php

namespace App\Models;

class YzResult {
  //状态 
  public $status;
  //返回信息
  public $message;

  public function toJson()
  {
    return json_encode($this, JSON_UNESCAPED_UNICODE);
  }

}
