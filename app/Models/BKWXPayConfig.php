<?php namespace App\Models;

class BKWXPayConfig extends YzResult {

  public $timestamp;
  public $nonceStr;
  public $package;
  public $signType;
  public $paySign;
}
