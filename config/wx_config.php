<?php

return[
    'APPID' => 'wx830b9a3dfb35d2a4',
    'APPSECRET' => '3b2789d71810de54c9c3bc55400b601a',
    'MCHID' => '商户平台ID',
    'KEY' => '商户平台32位密钥',
    //=======【证书路径设置】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var path
     */
    'SSLCERT_PATH' => '../cert/apiclient_cert.pem',
    'SSLKEY_PATH' => '../cert/apiclient_key.pem'
];

